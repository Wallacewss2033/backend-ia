<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_domain_id' => 'sometimes|required|exists:sites_domains,id',
            'author_id' => 'nullable|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'sometimes|required|string',
            'featured_image_url' => 'nullable|string|max:500',
            'featured_image_alt' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,in_review,approved,published,rejected',
            'published_at' => 'nullable|date',
        ];
    }
}
