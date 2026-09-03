<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_has_appointments_relation()
    {
        $user = User::factory()->create();
        $appointment = Appointment::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->appointments->contains($appointment));
        $this->assertInstanceOf(Appointment::class, $user->appointments->first());
    }
}
