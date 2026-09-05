<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoMetaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'article_id' => $this->article_id,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'canonical_url' => $this->canonical_url,
            'og_image_url' => $this->og_image_url,
            'robots_directives' => $this->robots_directives,
            'google_ads_conversion_label' => $this->google_ads_conversion_label,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'article' => new ArticleResource($this->whenLoaded('article')),
        ];
    }
}
