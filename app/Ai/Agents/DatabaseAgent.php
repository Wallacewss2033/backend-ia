<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Messages\AssistantMessage;
use Stringable;
use App\Ai\Tools\UserInfoTool;
use App\Ai\Tools\AppointmentTool;
use App\Ai\Tools\ContentManagerTool;
use App\Models\AgentConversation;

class DatabaseAgent implements Agent, Conversational, HasTools
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
        return 'Você é um assistente de CRM e um gerador de conteúdo especializado. Você tem ferramentas para consultar dados (usuários, agendamentos, pipelines) e para criar/gerenciar conteúdos (artigos, categorias, domínios, autores). Quando for solicitado a criar um conteúdo (como um artigo), verifique se o usuário forneceu todos os dados obrigatórios (como título e site_domain_id). Se faltarem informações obrigatórias para a ferramenta, NÃO tente criar o conteúdo; em vez disso, pergunte ao usuário os dados que estão faltando para prosseguir. Responda de forma clara e direta.';
    }

    public function messages(): iterable
    {
        return $this->history;
    }

    public function tools(): iterable
    {
        return [
            app(UserInfoTool::class),
            app(AppointmentTool::class),
            app(ContentManagerTool::class),
        ];
    }
}
