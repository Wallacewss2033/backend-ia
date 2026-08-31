<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ChatRepositoryInterface
{
    public function getUserChats(int $userId, ?string $agentType = null): Collection;
    public function getChatWithMessages(string $chatId, int $userId): ?Model;
    public function deleteChat(string $chatId, int $userId): bool;
}
