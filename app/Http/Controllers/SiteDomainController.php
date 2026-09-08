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
                $request->input('type'),
                'sites-domains'
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

    use \App\Traits\ManagesRenderDomains;

    public function verifyDns(int $id): JsonResponse
    {
        $siteDomain = $this->siteDomainService->getSiteDomainById($id);
        if (!$siteDomain) {
            return response()->json(['message' => 'Site não encontrado.'], 404);
        }

        $domain = $siteDomain->domain_url;

        // Verificação de Registro Ativo (DNS)
        if (checkdnsrr($domain, 'NS') || checkdnsrr($domain, 'A')) {
            
            $renderResult = $this->addDomainToRender($domain);

            if ($renderResult['success']) {
                $this->siteDomainService->updateSiteDomain($id, [
                    'status' => 'approved',
                    'dns_verified_at' => now(),
                ]);
                
                return response()->json([
                    'message' => $renderResult['message'],
                    'status' => 'approved',
                    'dns_verified_at' => now(),
                ]);
            }

            return response()->json([
                'message' => $renderResult['message'],
            ], $renderResult['status_code'] ?? 422);
        }

        return response()->json([
            'message' => 'O domínio não parece estar registrado ou ainda não propagou as entradas DNS.',
        ], 422);
    }
}
