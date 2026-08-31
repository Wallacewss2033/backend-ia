<?php

namespace App\Services;

use App\Ai\Agents\DatabaseAgent;
use App\Ai\Agents\DocumentAgent;
use App\Models\AgentConversation;
use App\Models\User;
use Illuminate\Support\Str;

class MessageService
{
    public function __construct(
        protected RagService $ragService
    ) {}

    public function sendMessage(string $prompt, User $user, ?string $conversationId = null, string $agentType = 'database'): array
    {
        if ($conversationId) {
            $conversation = AgentConversation::where('id', $conversationId)->where('user_id', $user->id)->firstOrFail();
            $agentType = $conversation->agent_type ?? 'database';
        } else {
            $conversation = AgentConversation::create([
                'id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'title' => Str::limit($prompt, 50),
                'agent_type' => $agentType
            ]);
        }

        // 1. Initialize agent
        $agent = $agentType === 'document' ? DocumentAgent::make($conversation) : DatabaseAgent::make($conversation);

        // 2. Fetch context from RAG ONLY if DocumentAgent
        $promptForAi = $prompt;
        
        if ($agentType === 'document') {
            $context = $this->ragService->retrieve($prompt, $user->id);
            if (!empty($context)) {
                $promptForAi = $context . "--- FIM DO CONTEXTO ---\n\nPergunta do usuário: " . $prompt;
            }
        }

        // 3. Save the original user prompt
        $conversation->messages()->create([
            'id' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $prompt,
            'agent' => $agentType,
            'attachments' => [],
            'tool_calls' => [],
            'tool_results' => [],
            'usage' => [],
            'meta' => [],
        ]);

        // 4. Send the augmented prompt to the AI
        $response = $agent->prompt($promptForAi);

        $conversation->messages()->create([
            'id' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'role' => 'agent',
            'content' => $response->text,
            'agent' => $agentType,
            'attachments' => [],
            'tool_calls' => [],
            'tool_results' => [],
            'usage' => [],
            'meta' => [],
        ]);

        $conversation->touch();

        return [
            'conversation_id' => $conversation->id,
            'reply' => $response->text,
        ];
    }
}
