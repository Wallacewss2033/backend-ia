<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteDomainRequest;
use App\Http\Requests\UpdateSiteDomainRequest;
use App\Http\Requests\UploadSiteDomainImageRequest;
use App\Http\Resources\SiteDomainResource;
use App\Services\SiteDomainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SiteDomainController extends Controller
{
    protected SiteDomainService $siteDomainService;

    public function __construct(SiteDomainService $siteDomainService)
    {
        $this->siteDomainService = $siteDomainService;
    }

    public function index(): AnonymousResourceCollection
    {
        $siteDomains = $this->siteDomainService->getAllSiteDomains();
        return SiteDomainResource::collection($siteDomains);
    }

    public function store(StoreSiteDomainRequest $request): SiteDomainResource
    {
        $siteDomain = $this->siteDomainService->createSiteDomain($request->validated());
        return new SiteDomainResource($siteDomain);
    }

    public function show(int $id): SiteDomainResource
    {
        $siteDomain = $this->siteDomainService->getSiteDomainById($id);
        return new SiteDomainResource($siteDomain);
    }

    public function update(UpdateSiteDomainRequest $request, int $id): JsonResponse
    {
        $this->siteDomainService->updateSiteDomain($id, $request->validated());
        $siteDomain = $this->siteDomainService->getSiteDomainById($id);
        
        return response()->json(new SiteDomainResource($siteDomain));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->siteDomainService->deleteSiteDomain($id);
        return response()->json(null, 204);
    }

    public function uploadImage(UploadSiteDomainImageRequest $request): JsonResponse
    {
        try {
            $result = $this->siteDomainService->uploadImageToFirebase(
                $request->file('image'),
                $request->input('type')
            );

            return response()->json([
                'message' => 'Upload realizado com sucesso!',
                'url' => $result['url'],
                'path' => $result['path']
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function verifyDns(int $id): JsonResponse
    {
        $siteDomain = $this->siteDomainService->getSiteDomainById($id);
        if (!$siteDomain) {
            return response()->json(['message' => 'Site não encontrado.'], 404);
        }

        $domain = $siteDomain->domain_url;

        try {
            // Requisição simples para garantir que o SSL está ativo e retorna 200 OK
            // O timeout garante que não trave a API se o servidor estiver inacessível
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://' . $domain);
            
            if ($response->successful()) {
                $this->siteDomainService->updateSiteDomain($id, [
                    'status' => 'approved',
                    'dns_verified_at' => now(),
                ]);
                
                return response()->json([
                    'message' => 'Domínio e SSL verificados com sucesso!',
                    'status' => 'approved',
                    'dns_verified_at' => now(),
                ]);
            }
            
            return response()->json([
                'message' => 'Domínio respondeu, mas não retornou sucesso (Código ' . $response->status() . ').',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Não foi possível conectar ao domínio via HTTPS. Verifique se os apontamentos DNS já propagaram e se o SSL foi emitido.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
