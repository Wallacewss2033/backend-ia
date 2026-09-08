<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ManagesRenderDomains
{
    /**
     * Adiciona um domínio customizado ao serviço no Render.
     *
     * @param string $domain
     * @return array
     */
    public function addDomainToRender(string $domain): array
    {
        $renderApiKey = config('services.render.api_key');
        $renderServiceId = config('services.render.service_id');

        if (!$renderApiKey || !$renderServiceId) {
            return [
                'success' => false,
                'message' => 'DNS verificado, mas as credenciais da API do Render (RENDER_API_KEY ou RENDER_SERVICE_ID) não estão configuradas no .env.',
                'status_code' => 500,
            ];
        }

        try {
            $response = Http::withToken($renderApiKey)
                ->acceptJson()
                ->post("https://api.render.com/v1/services/{$renderServiceId}/custom-domains", [
                    'name' => $domain
                ]);

            if ($response->successful() || $response->status() === 400) {
                // 201 Created = Sucesso | 400 Bad Request = Geralmente domínio já existe
                return [
                    'success' => true,
                    'message' => 'Domínio registrado, DNS verificado e vinculado ao Render com sucesso!',
                ];
            }

            return [
                'success' => false,
                'message' => 'DNS OK, mas falha ao vincular no Render: ' . $response->body(),
                'status_code' => 422,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro de comunicação com a API do Render: ' . $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }
}
