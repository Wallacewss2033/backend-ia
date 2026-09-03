<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        $hoje = \Carbon\Carbon::now()->format('d/m/Y');
        return "Gerenciar agendamentos e compromissos: permite listar compromissos de uma data específica ou criar novos agendamentos no calendário. Importante: Considere que a data de hoje é {$hoje}.";
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $action = $request['action'] ?? null;
        
        $userId = auth()->id() ?? 1; // Default user_id se não estiver autenticado

        if ($action === 'list_events') {
            $date = $request['date'] ?? null;
            if (!$date) {
                return 'Por favor, informe a data (date) no formato YYYY-MM-DD.';
            }

            $appointments = Appointment::where('user_id', $userId)
                ->whereDate('start_time', $date)
                ->orderBy('start_time')
                ->get();

            if ($appointments->isEmpty()) {
                $formattedDate = Carbon::parse($date)->format('d/m');
                return "Você não tem compromissos para {$formattedDate}.";
            }

            $formattedDate = Carbon::parse($date)->format('d/m');
            $response = "Na data ({$formattedDate}), você tem os seguintes compromissos:\n";
            
            foreach ($appointments as $appointment) {
                $start = Carbon::parse($appointment->start_time)->format('H:i');
                $end = Carbon::parse($appointment->end_time)->format('H:i');
                $response .= "- {$start} às {$end}: {$appointment->title}\n";
            }

            return $response;
        }

        if ($action === 'create_event') {
            $startTime = $request['start_time'] ?? null;
            $endTime = $request['end_time'] ?? null;
            $title = $request['title'] ?? null;
            $clientName = $request['client_name'] ?? null;

            if (!$startTime || !$endTime || !$title) {
                return 'Por favor, forneça start_time, end_time e title para criar o compromisso.';
            }

            $appointment = Appointment::create([
                'user_id' => $userId,
                'title' => $title,
                'description' => $clientName ? "Cliente: {$clientName}" : null,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);

            $start = Carbon::parse($startTime)->format('d/m/Y às H:i');
            return "Compromisso '{$title}' agendado com sucesso para {$start}.";
        }

        return 'Ação não reconhecida. Use "list_events" ou "create_event".';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->enum(['list_events', 'create_event'])
                ->description('Ação a executar: "list_events" para ver compromissos, ou "create_event" para criar um agendamento.')
                ->required(),

            // Parâmetros para action = "list_events"
            'date' => $schema->string()
                ->description('Data para listar os eventos no formato YYYY-MM-DD. Ex: 2026-09-02')
                ->required(),

            // Parâmetros para action = "create_event"
            'start_time' => $schema->string()
                ->description('Data e hora de início no formato YYYY-MM-DD HH:mm:ss. Ex: 2026-09-03 10:00:00')
                ->required(),
                
            'end_time' => $schema->string()
                ->description('Data e hora de término no formato YYYY-MM-DD HH:mm:ss. Ex: 2026-09-03 11:00:00')
                ->required(),
                
            'title' => $schema->string()
                ->description('Título ou assunto da reunião.')
                ->required(),
                
            'client_name' => $schema->string()
                ->description('Nome do cliente (opcional).')
                ->required(),
        ];
    }
}
