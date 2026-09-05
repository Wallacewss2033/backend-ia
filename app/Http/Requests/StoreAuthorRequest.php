<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_domain_id' => 'required|exists:sites_domains,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'avatar_url' => 'nullable|string|max:500',
            'social_links' => 'nullable|array',
        ];
    }
}
