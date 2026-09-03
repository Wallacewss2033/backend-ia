<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use App\Services\UserService;
use App\Models\User;
use Carbon\Carbon;

class UserInfoTool implements Tool
{
    public function __construct(protected UserService $userService)
    {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Buscar informações sobre a tabela de usuários (como o total de usuários) ou de um usuário específico por qualquer campo (ex: email, id, name).';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $action = $request['action'] ?? null;

        // 1. Ação: Contagem Geral
        if ($action === 'count') {
            $total = User::count();
            return "O total de usuários cadastrados é: {$total}";
        }

        // 2. Ação: Buscar por campo único (email, name, id)
        if ($action === 'get') {
            $field = $request['field'] ?? null;
            $value = $request['value'] ?? null;

            if (!$field || !$value) {
                return 'Forneça o campo (field) e o valor (value) para buscar.';
            }

            $user = User::where($field, $value)->first();

            if (!$user) {
                return "Usuário não encontrado com {$field} = {$value}";
            }

            return "Nome: {$user->name} | Email: {$user->email} | Cadastrado em: " . $user->created_at->format('d/m/Y H:i:s');
        }

        // 3. Ação: Analisar por Data / Hora (created_at)
        if ($action === 'filter_by_date') {
            $operator = $request['date_operator'] ?? 'exact_date';
            $startDate = $request['start_date'] ?? null;
            $endDate = $request['end_date'] ?? null;

            $query = User::query();

            switch ($operator) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    $descricao = "cadastrados hoje";
                    break;

                case 'exact_date':
                    if (!$startDate) return 'Informe a data (start_date) no formato YYYY-MM-DD.';
                    $query->whereDate('created_at', Carbon::parse($startDate)->toDateString());
                    $descricao = "cadastrados em " . Carbon::parse($startDate)->format('d/m/Y');
                    break;

                case 'after':
                    if (!$startDate) return 'Informe a data inicial (start_date).';
                    // Se tiver hora (ex: 2026-08-28 14:00:00), compara com horário; senão compara após o final do dia anterior
                    $query->where('created_at', '>=', Carbon::parse($startDate));
                    $descricao = "cadastrados a partir de " . Carbon::parse($startDate)->format('d/m/Y H:i');
                    break;

                case 'before':
                    if (!$startDate) return 'Informe a data limite (start_date).';
                    $query->where('created_at', '<=', Carbon::parse($startDate));
                    $descricao = "cadastrados até " . Carbon::parse($startDate)->format('d/m/Y H:i');
                    break;

                case 'between':
                    if (!$startDate || !$endDate) return 'Informe start_date e end_date para o intervalo.';
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                    $descricao = "cadastrados entre {$start->format('d/m/Y')} e {$end->format('d/m/Y')}";
                    break;

                default:
                    return 'Operador de data inválido.';
            }

            $hourOperator = $request['hour_operator'] ?? null;
            $hour = $request['hour'] ?? null;
            $endHour = $request['end_hour'] ?? null;
            
            switch ($hourOperator) {
                case 'exact_hour':
                    if (!$hour) return 'Informe a hora (hour) no formato HH.';
                    
                    $parsed = Carbon::parse($hour);
                    
                    // Se foi informada apenas a hora (ex: "01") ou hora zerada (ex: "01:00:00"), buscar na faixa daquela hora.
                    if (strlen(trim(str_replace('h', '', $hour))) <= 2 || ($parsed->minute === 0 && $parsed->second === 0)) {
                        $query->whereRaw('HOUR(created_at) = ?', [$parsed->hour]);
                        $descricao .= " na faixa das " . $parsed->format('H') . "h";
                    } elseif ($parsed->second === 0) {
                        // Se informou hora e minuto, mas não os segundos (ex: "01:16"), busca em qualquer segundo daquele minuto.
                        $query->whereRaw('HOUR(created_at) = ? AND MINUTE(created_at) = ?', [$parsed->hour, $parsed->minute]);
                        $descricao .= " por volta das " . $parsed->format('H:i');
                    } else {
                        $query->whereTime('created_at', $parsed->format('H:i:s'));
                        $descricao .= " exatamente às " . $parsed->format('H:i:s');
                    }
                    break;

                case 'after':
                    if (!$hour) return 'Informe a hora inicial (hour).';
                    $query->whereTime('created_at', '>=', Carbon::parse($hour)->format('H:i:s'));
                    $descricao .= " a partir de " . Carbon::parse($hour)->format('H:i:s');
                    break;

                case 'before':
                    if (!$hour) return 'Informe a hora limite (hour).';
                    $query->whereTime('created_at', '<=', Carbon::parse($hour)->format('H:i:s'));
                    $descricao .= " até " . Carbon::parse($hour)->format('H:i:s');
                    break;

                case 'between':
                    if (!$hour || !$endHour) return 'Informe hour e end_hour para o intervalo.';
                    $query->whereTime('created_at', '>=', Carbon::parse($hour)->format('H:i:s'));
                    $query->whereTime('created_at', '<=', Carbon::parse($endHour)->format('H:i:s'));
                    $descricao .= " entre {$hour} e {$endHour}";
                    break;

                default:
                    return 'Operador de hora inválido.';
            }

            $users = $query->limit(10)->get();
            $totalEncontrados = $query->count();

            if ($users->isEmpty()) {
                return "Nenhum usuário encontrado {$descricao}.";
            }

            $resultado = "Total de {$totalEncontrados} usuário(s) {$descricao}:\n";
            foreach ($users as $user) {
                $resultado .= "- {$user->name} ({$user->email}) - Criado em: {$user->created_at->format('d/m/Y H:i')}\n";
            }

            if ($totalEncontrados > 10) {
                $resultado .= "... e mais " . ($totalEncontrados - 10) . " usuário(s).";
            }

            return $resultado;
        }

        return "Ação não reconhecida.";
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->description('Ação a executar: "count" (total geral), "get" (buscar por campo único), "filter_by_date" (filtrar por data/hora de cadastro), ou "filter_by_hour" (filtrar por hora de cadastro).')
                ->required(),

            // Parâmetros para action = "get"
            'field' => $schema->string()->description('Campo de busca (ex: id, email, name).')->required(),
            'value' => $schema->string()->description('Valor do campo para busca exata.')->required(),

            // Parâmetros para action = "filter_by_date"
            'date_operator' => $schema->string()
                ->enum(['exact_date', 'after', 'before', 'between', 'today'])
                ->description('Tipo de comparação temporal no created_at: exact_date (data exata), after (depois de), before (antes de), between (intervalo), today (hoje).')
                ->required(),

            'start_date' => $schema->string()
                ->description('Data inicial ou exata no formato "YYYY-MM-DD" ou "YYYY-MM-DD HH:mm:ss".')
                ->required(),

            'end_date' => $schema->string()
                ->description('Data final no formato "YYYY-MM-DD" (obrigatório apenas se date_operator = "between").')
                ->required(),
            
            'hour_operator' => $schema->string()
                ->enum(['exact_hour', 'before', 'after'])
                ->description('Tipo de comparação temporal no created_at: exact_hour (hora exata), before (antes de), after (depois de).')
                ->required(),

            'hour' => $schema->string()
                ->description('Hora no formato "HH" ou "HH:mm:ss".')
                ->required(),

            'end_hour' => $schema->string()
                ->description('Hora final no formato "HH" ou "HH:mm:ss" (obrigatório apenas se hour_operator = "between").')
                ->required(),
            ];
    }
}
