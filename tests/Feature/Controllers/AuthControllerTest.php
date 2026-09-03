<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'email'],
                     'access_token',
                     'token_type'
                 ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'wrongpassword',
        ]);

        // ValidationException returns 422 Unprocessable Entity by default in Laravel
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_logout_invalidates_token()
    {
        $user = User::factory()->create();
        
        // Crio um token real usando o Sanctum para o teste de logout
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Deslogado com sucesso'
                 ]);
                 
        // Verifica se o token foi apagado do banco
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_fails_if_unauthenticated()
    {
        $response = $this->postJson('/api/logout');

        // Middleware auth:sanctum deve barrar a requisição e retornar 401
        $response->assertStatus(401);
    }
}
