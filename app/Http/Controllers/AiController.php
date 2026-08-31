<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiService;
use Exception;

class AiController extends Controller
{
    public function __construct(protected AiService $aiService)
    {}

    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        try {
            $reply = $this->aiService->chat($request->prompt);

            return response()->json([
                'reply' => $reply,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'reply' => 'Desculpe, nossa IA está sobrecarregada no momento. Por favor, tente novamente em alguns instantes.',
                'error' => $e->getMessage(),
            ], 503);
        }
    }
}
