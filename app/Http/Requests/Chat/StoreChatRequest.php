<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => 'required|string',
            'conversation_id' => 'nullable|string|uuid|exists:agent_conversations,id',
            'agent_type' => 'nullable|string|in:database,document',
        ];
    }
}
