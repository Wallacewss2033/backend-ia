<?php

namespace App\Repositories\Eloquent;

use App\Models\AgentConversation;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChatRepository implements ChatRepositoryInterface
{
    public function getUserChats(int $userId, ?string $agentType = null): Collection
    {
        $query = AgentConversation::where('user_id', $userId)
            ->with(['messages' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('updated_at', 'desc');

        if ($agentType) {
            $query->where('agent_type', $agentType);
        }

        return $query->get();
    }

    public function getChatWithMessages(string $chatId, int $userId): ?Model
    {
        return AgentConversation::where('id', $chatId)
            ->where('user_id', $userId)
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at');
            }])
            ->first();
    }

    public function deleteChat(string $chatId, int $userId): bool
    {
        $chat = AgentConversation::where('id', $chatId)
            ->where('user_id', $userId)
            ->first();

        if ($chat) {
            // Because cascade delete might not be configured, we delete messages first
            $chat->messages()->delete();
            return $chat->delete();
        }

        return false;
    }
}
