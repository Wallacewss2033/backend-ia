<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteDomainResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'domain_url' => $this->domain_url,
            'title' => $this->title,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'favicon_url' => $this->favicon_url,
            'primary_color' => $this->primary_color,
            'status' => $this->status,
            'dns_verified_at' => $this->dns_verified_at,
            'google_ads_id' => $this->google_ads_id,
            'google_tag_manager_id' => $this->google_tag_manager_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
