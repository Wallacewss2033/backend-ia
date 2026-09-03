<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Mockery;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_login_returns_token_with_valid_credentials()
    {
        // Criar um usuário real no banco com senha conhecida
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123', // Será feito hash pelo model
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // O método login usa o createToken do Sanctum, que precisa do banco
        $result = $this->authService->login($credentials);

        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals($user->id, $result['user']->id);
    }

    public function test_login_throws_validation_exception_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $this->expectException(ValidationException::class);
        $this->authService->login($credentials);
    }

    public function test_login_throws_validation_exception_with_invalid_email()
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $this->expectException(ValidationException::class);
        $this->authService->login($credentials);
    }

    public function test_logout_deletes_current_access_token()
    {
        $user = User::factory()->create();
        
        // Mocking the user and its currentAccessToken method
        $tokenMock = Mockery::mock();
        $tokenMock->shouldReceive('delete')->once()->andReturn(true);

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldReceive('currentAccessToken')->once()->andReturn($tokenMock);

        $result = $this->authService->logout($userMock);

        $this->assertTrue($result);
    }
}
