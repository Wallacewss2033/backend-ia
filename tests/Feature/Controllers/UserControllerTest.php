<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Crio um usuário para autenticar as requisições se a API for protegida.
        // Assumindo que usa Sanctum:
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_index_returns_all_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
                 ]);
        
        // Verifica se ao menos 4 usuários estão na resposta (o auth + 3)
        $this->assertGreaterThanOrEqual(4, count($response->json()));
    }

    public function test_store_creates_new_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'New User',
                     'email' => 'new@example.com',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com'
        ]);
    }

    public function test_show_returns_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email,
                 ]);
    }

    public function test_update_modifies_user()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email, // mantem o email para passar no request unique
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Updated Name',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_destroy_deletes_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
