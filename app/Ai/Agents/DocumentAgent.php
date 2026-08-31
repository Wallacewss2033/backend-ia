<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Messages\AssistantMessage;
use Stringable;
use App\Models\AgentConversation;

class DocumentAgent implements Agent, Conversational
{
    use Promptable;

    protected array $history = [];

    public function __construct(?AgentConversation $conversation = null) 
    {
        if ($conversation) {
            $this->history = $conversation->messages()->orderBy('created_at')->get()->map(function ($msg) {
                if ($msg->role === 'user') {
                    return new UserMessage($msg->content);
                }
                return new AssistantMessage($msg->content);
            })->toArray();
        }
    }

    public function provider(): string
    {
        return 'gemini';
    }

    public function instructions(): Stringable|string
    {
        return 'Você é um assistente de inteligência artificial especializado na Base de Conhecimento do usuário. O usuário vai enviar perguntas, e junto com a pergunta, um sistema (RAG) irá injetar um contexto extraído dos documentos dele. Use APENAS o contexto fornecido para responder com precisão. Sempre cite a Fonte e a Página da informação que você utilizou.';
    }

    public function messages(): iterable
    {
        return $this->history;
    }
}
