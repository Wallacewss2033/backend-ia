<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeoMetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => 'required|exists:articles,id|unique:seo_metas,article_id',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'canonical_url' => 'nullable|string|max:500',
            'og_image_url' => 'nullable|string|max:500',
            'robots_directives' => 'nullable|string|max:50',
            'google_ads_conversion_label' => 'nullable|string|max:100',
        ];
    }
}
