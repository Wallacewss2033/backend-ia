<?php

namespace Tests\Unit\Repositories;

use App\Models\Appointment;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AppointmentRepository();
    }

    public function test_get_all_for_user_returns_user_appointments()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Appointment::factory()->count(3)->create(['user_id' => $user1->id]);
        Appointment::factory()->count(2)->create(['user_id' => $user2->id]);

        $appointments = $this->repository->getAllForUser($user1->id);

        $this->assertCount(3, $appointments);
        $this->assertEquals($user1->id, $appointments->first()->user_id);
    }

    public function test_find_by_id_returns_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['user_id' => $user->id]);

        $found = $this->repository->findById($appointment->id);

        $this->assertNotNull($found);
        $this->assertEquals($appointment->id, $found->id);
    }

    public function test_create_saves_new_appointment()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'title' => 'Reunião de Alinhamento',
            'description' => 'Discussão sobre testes.',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
        ];

        $appointment = $this->repository->create($data);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertDatabaseHas('appointments', [
            'title' => 'Reunião de Alinhamento',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_modifies_existing_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['user_id' => $user->id, 'title' => 'Titulo Antigo']);

        $result = $this->repository->update($appointment, ['title' => 'Titulo Novo']);

        $this->assertTrue($result);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'title' => 'Titulo Novo',
        ]);
    }

    public function test_delete_removes_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->delete($appointment);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id,
        ]);
    }
}
