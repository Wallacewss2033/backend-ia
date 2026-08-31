<?php

namespace App\Services;

use App\Ai\Agents\SupportAgent;

class AiService
{
    public function chat(string $prompt): string
    {
        $response = SupportAgent::make()->prompt($prompt);
        return $response->text;
    }
}
