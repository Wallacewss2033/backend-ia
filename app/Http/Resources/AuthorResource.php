<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'site_domain_id' => $this->site_domain_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'social_links' => $this->social_links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'site_domain' => new SiteDomainResource($this->whenLoaded('siteDomain')),
        ];
    }
}
