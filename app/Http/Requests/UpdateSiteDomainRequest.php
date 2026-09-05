<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain_url' => 'sometimes|required|string|max:255|unique:sites_domains,domain_url,' . $this->route('id'),
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string|max:500',
            'favicon_url' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'status' => 'nullable|in:draft,in_review,approved,rejected,suspended',
            'dns_verified_at' => 'nullable|date',
            'google_ads_id' => 'nullable|string|max:50',
            'google_tag_manager_id' => 'nullable|string|max:50',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('domain_url') && is_string($this->domain_url)) {
            $domain = $this->domain_url;
            $domain = preg_replace('#^https?://#', '', $domain);
            $domain = explode('/', $domain)[0];
            $domain = preg_replace('#^www\.#', '', $domain);
            $domain = strtolower(trim($domain));

            $this->merge([
                'domain_url' => $domain,
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->has('domain_url') || empty($this->domain_url)) {
                return;
            }

            $domain = $this->domain_url;
            
            if (!preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/', $domain)) {
                $validator->errors()->add('domain_url', 'O formato do domínio é inválido. Digite um domínio como "seusite.com.br".');
                return;
            }

            if (!checkdnsrr($domain, 'NS') && !checkdnsrr($domain, 'A')) {
                $validator->errors()->add('domain_url', 'O domínio não parece estar registrado ou não possui entradas DNS ativas.');
            }
        });
    }
}
