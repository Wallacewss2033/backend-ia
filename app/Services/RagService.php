<?php

namespace App\Services;

use App\Models\DocumentChunk;
use Laravel\Ai\Ai;

class RagService
{
    /**
     * Recupera os trechos mais relevantes baseados na pergunta.
     */
    public function retrieve(string $prompt, int $userId, int $topK = 1): string
    {
        // 1. Gera o embedding da pergunta
        $response = Ai::embeddingProvider('gemini')->embeddings([$prompt]);
        $promptEmbedding = $response->first() ?? null;

        if (!$promptEmbedding) {
            return '';
        }

        // 2. Busca todos os chunks do usuário cujos documentos estão completos
        $chunks = DocumentChunk::whereHas('document', function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('status', 'completed');
        })->with('document:id,title')->get();

        if ($chunks->isEmpty()) {
            return '';
        }

        // 3. Calcula a similaridade
        $scoredChunks = [];
        foreach ($chunks as $chunk) {
            $chunkEmbedding = $chunk->embedding;
            if (!is_array($chunkEmbedding) || empty($chunkEmbedding)) {
                continue;
            }

            $score = $this->cosineSimilarity($promptEmbedding, $chunkEmbedding);
            $scoredChunks[] = [
                'score' => $score,
                'content' => $chunk->content,
                'title' => $chunk->document->title,
                'page' => $chunk->page_number,
            ];
        }

        // 4. Ordena pelo maior score e pega os topK
        usort($scoredChunks, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        $topChunks = array_slice($scoredChunks, 0, $topK);

        // 5. Formata a string de contexto
        $contextString = "Aqui estão trechos extraídos dos documentos do usuário para embasar sua resposta (Sempre que utilizar a informação cite a Fonte e a Página):\n\n";
        
        $hasRelevant = false;
        foreach ($topChunks as $item) {
            // Voltando para um filtro mais brando de relevância (0.50) para garantir que o contexto chegue ao agente
            if ($item['score'] > 0.50) { 
                $hasRelevant = true;
                $pageText = $item['page'] ? " (Página {$item['page']})" : "";
                $contextString .= "- Fonte: {$item['title']}{$pageText} [Score: " . round($item['score'], 2) . "]\n";
                $contextString .= "Trecho: \"{$item['content']}\"\n\n";
            }
        }
        \Log::info('Contexto: ' . $contextString);
        return $hasRelevant ? $contextString : '';
    }

    /**
     * Calcula a similaridade de cosseno entre dois vetores.
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        
        $count = min(count($vecA), count($vecB));
        
        for ($i = 0; $i < $count; $i++) {
            $a = (float)($vecA[$i] ?? 0);
            $b = (float)($vecB[$i] ?? 0);
            
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
