<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_domain_id' => 'sometimes|required|exists:sites_domains,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'avatar_url' => 'nullable|string|max:500',
            'social_links' => 'nullable|array',
        ];
    }
}
