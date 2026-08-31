<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;
use Exception;

class MessageController extends Controller
{
    public function __construct(protected MessageService $messageService)
    {}

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'conversation_id' => 'nullable|string',
            'agent_type' => 'nullable|string|in:database,document',
        ]);

        try {
            $result = $this->messageService->sendMessage(
                $validated['prompt'], 
                $request->user(), 
                $validated['conversation_id'] ?? null,
                $validated['agent_type'] ?? 'database'
            );

            return response()->json([
                'reply' => $result['reply'],
                'conversation_id' => $result['conversation_id'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'reply' => 'Desculpe, nossa IA está sobrecarregada no momento. Por favor, tente novamente em alguns instantes.',
                'error' => $e->getMessage(),
            ], 503);
        }
    }
}
