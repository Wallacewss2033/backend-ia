<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Ai;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public function __construct(
        public Document $document
    ) {}

    public function handle(): void
    {
        try {
            $this->document->update(['status' => 'processing']);

            // 1. Ler o arquivo físico
            $path = Storage::disk('public')->path($this->document->file_path);
            
            // Extração de texto e chunks associados às páginas
            $mime = $this->document->mime_type;
            $chunksWithMeta = [];
            
            // Permite configurar o tamanho do chunk via config, ou usar 1000 como padrão
            $chunkSize = config('services.ai.chunk_size', 1000);
            
            if ($mime === 'application/pdf') {
                // Requer: composer require smalot/pdfparser
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                
                $pages = $pdf->getPages();
                foreach ($pages as $pageIndex => $page) {
                    $pageText = $page->getText();
                    if (trim($pageText)) {
                        // Passamos um overlap de 200 caracteres
                        $pageChunks = $this->chunkText($pageText, $chunkSize, 200);
                        foreach ($pageChunks as $text) {
                            $chunksWithMeta[] = [
                                'content' => $text,
                                'page_number' => $pageIndex + 1, // 1-indexed
                            ];
                        }
                    }
                }
            } else {
                $content = file_get_contents($path);
                if ($content && trim($content)) {
                    // Passamos um overlap de 200 caracteres para evitar que frases sejam cortadas no meio
                    $pageChunks = $this->chunkText($content, $chunkSize, 200);
                    foreach ($pageChunks as $text) {
                        $chunksWithMeta[] = [
                            'content' => $text,
                            'page_number' => null,
                        ];
                    }
                }
            }
            
            if (empty($chunksWithMeta)) {
                throw new \Exception("Conteúdo do documento está vazio ou não pôde ser lido.");
            }
            
            // Extrair apenas os textos para enviar em lote para a API
            $textsToEmbed = array_column($chunksWithMeta, 'content');
            
            // Fazer APENAS 1 chamada à API com todos os chunks (Batching)
            $embeddingResponse = Ai::embeddingProvider('gemini')->embeddings($textsToEmbed);
            $embeddingsList = $embeddingResponse->embeddings ?? [];

            foreach ($chunksWithMeta as $index => $chunkData) {
                $this->document->chunks()->create([
                    'content' => $chunkData['content'],
                    'chunk_index' => $index,
                    'page_number' => $chunkData['page_number'],
                    // Pegamos o vetor correspondente àquele chunk na lista que a API retornou
                    'embedding' => $embeddingsList[$index] ?? [],
                ]);
            }

            // 5. Atualizar o Documento para concluído
            $this->document->update([
                'status' => 'completed',
                'total_chunks' => count($chunksWithMeta),
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao processar documento ID {$this->document->id}: " . $e->getMessage());
            
            $this->document->update([
                'status' => 'failed'
            ]);
        }
    }

    /**
     * Função para dividir texto em chunks com sobreposição (overlap).
     */
    private function chunkText(string $text, int $chunkSize, int $overlap = 200): array
    {
        // Remove espaços extras
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        $chunks = [];
        $length = mb_strlen($text);
        
        if ($length === 0) return $chunks;

        $i = 0;
        while ($i < $length) {
            $chunks[] = mb_substr($text, $i, $chunkSize);
            
            // Avança o cursor, mas retrocede o valor do overlap para garantir contexto contínuo
            $i += $chunkSize - $overlap;
            
            // Previne loops infinitos caso o overlap seja configurado erroneamente maior que o chunk
            if ($chunkSize <= $overlap) {
                $i += $chunkSize; 
            }
        }
        
        return $chunks;
    }
}
