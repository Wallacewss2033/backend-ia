<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_can_be_created()
    {
        $user = User::factory()->create();
        
        $appointment = Appointment::factory()->create([
            'user_id' => $user->id,
            'title' => 'Reunião Importante',
        ]);

        $this->assertDatabaseHas('appointments', [
            'title' => 'Reunião Importante',
            'user_id' => $user->id,
        ]);
    }

    public function test_appointment_belongs_to_user_relation()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $appointment->user);
        $this->assertEquals($user->id, $appointment->user->id);
    }
}
