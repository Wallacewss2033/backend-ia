<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(1)->addHours(1)->format('Y-m-d H:i:s'),
            'google_event_id' => null,
            'meet_link' => null,
        ];
    }
}
