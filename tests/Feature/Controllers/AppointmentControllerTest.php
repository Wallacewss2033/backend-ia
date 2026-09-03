<?php

namespace Tests\Feature\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_index_returns_user_appointments()
    {
        Appointment::factory()->count(2)->create(['user_id' => $this->user->id]);
        
        $otherUser = User::factory()->create();
        Appointment::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/appointments');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'start_time', 'end_time']
                     ]
                 ]);
    }

    public function test_store_creates_appointment_for_authenticated_user()
    {
        $payload = [
            'title' => 'Reunião de Vendas',
            'description' => 'Apresentação do produto',
            'start_time' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(1)->addHours(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/appointments', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title' => 'Reunião de Vendas',
                 ]);

        $this->assertDatabaseHas('appointments', [
            'user_id' => $this->user->id,
            'title' => 'Reunião de Vendas',
        ]);
    }

    public function test_update_modifies_existing_appointment()
    {
        $appointment = Appointment::factory()->create(['user_id' => $this->user->id]);

        $payload = [
            'title' => 'Reunião Atualizada',
            'start_time' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->putJson("/api/appointments/{$appointment->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title' => 'Reunião Atualizada',
                 ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'title' => 'Reunião Atualizada',
        ]);
    }

    public function test_update_returns_404_if_not_found()
    {
        $payload = [
            'title' => 'Reunião Atualizada',
            'start_time' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->putJson("/api/appointments/9999", $payload);

        $response->assertStatus(404);
    }

    public function test_destroy_deletes_appointment()
    {
        $appointment = Appointment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/appointments/{$appointment->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Appointment deleted successfully.'
                 ]);

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }

    public function test_destroy_returns_404_if_not_found()
    {
        $response = $this->deleteJson("/api/appointments/9999");
        $response->assertStatus(404);
    }
}
