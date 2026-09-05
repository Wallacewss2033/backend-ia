<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeoMetaRequest;
use App\Http\Requests\UpdateSeoMetaRequest;
use App\Http\Resources\SeoMetaResource;
use App\Services\SeoMetaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SeoMetaController extends Controller
{
    protected SeoMetaService $seoMetaService;

    public function __construct(SeoMetaService $seoMetaService)
    {
        $this->seoMetaService = $seoMetaService;
    }

    public function index(): AnonymousResourceCollection
    {
        $seoMetas = $this->seoMetaService->getAllSeoMetas();
        return SeoMetaResource::collection($seoMetas);
    }

    public function store(StoreSeoMetaRequest $request): SeoMetaResource
    {
        $seoMeta = $this->seoMetaService->createSeoMeta($request->validated());
        return new SeoMetaResource($seoMeta);
    }

    public function show(int $id): SeoMetaResource
    {
        $seoMeta = $this->seoMetaService->getSeoMetaById($id);
        return new SeoMetaResource($seoMeta);
    }

    public function update(UpdateSeoMetaRequest $request, int $id): JsonResponse
    {
        $this->seoMetaService->updateSeoMeta($id, $request->validated());
        $seoMeta = $this->seoMetaService->getSeoMetaById($id);
        
        return response()->json(new SeoMetaResource($seoMeta));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->seoMetaService->deleteSeoMeta($id);
        return response()->json(null, 204);
    }
}
