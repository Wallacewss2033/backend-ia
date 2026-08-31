<?php

namespace App\Services;

use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChatService
{
    public function __construct(protected ChatRepositoryInterface $chatRepository)
    {}

    public function getUserChats(int $userId, ?string $agentType = null): Collection
    {
        return $this->chatRepository->getUserChats($userId, $agentType);
    }

    public function getChatDetails(string $chatId, int $userId): ?Model
    {
        return $this->chatRepository->getChatWithMessages($chatId, $userId);
    }

    public function deleteChat(string $chatId, int $userId): bool
    {
        return $this->chatRepository->deleteChat($chatId, $userId);
    }
}
