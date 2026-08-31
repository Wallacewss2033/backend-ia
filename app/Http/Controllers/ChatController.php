<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreChatRequest;
use App\Http\Resources\ChatResource;
use App\Services\MessageService;
use App\Services\ChatService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected MessageService $messageService,
        protected ChatService $chatService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $agentType = $request->query('agent_type');
        $chats = $this->chatService->getUserChats($request->user()->id, $agentType);
        
        return response()->json([
            'data' => ChatResource::collection($chats)
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $chat = $this->chatService->getChatDetails($id, $request->user()->id);

        if (!$chat) {
            return response()->json(['message' => 'Chat não encontrado'], 404);
        }

        return response()->json([
            'data' => new ChatResource($chat)
        ]);
    }

    public function store(StoreChatRequest $request): JsonResponse
    {
        try {
            $result = $this->messageService->sendMessage(
                $request->validated('prompt'),
                $request->user(),
                $request->validated('conversation_id'),
                $request->validated('agent_type') ?? 'database'
            );

            return response()->json([
                'conversation_id' => $result['conversation_id'],
                'reply' => $result['reply'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'reply' => 'Desculpe, nossa IA está sobrecarregada no momento. Por favor, tente novamente em alguns instantes.',
                'error' => $e->getMessage(),
            ], 503);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $deleted = $this->chatService->deleteChat($id, $request->user()->id);

        if (!$deleted) {
            return response()->json(['message' => 'Chat não encontrado ou já excluído'], 404);
        }

        return response()->json(['message' => 'Chat excluído com sucesso']);
    }
}
