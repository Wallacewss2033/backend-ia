<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    protected $userRepositoryMock;
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_users()
    {
        $collection = new Collection([new User(), new User()]);
        
        $this->userRepositoryMock
            ->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->userService->getAllUsers();
        
        $this->assertEquals($collection, $result);
    }

    public function test_get_user_by_id()
    {
        $user = new User(['id' => 1, 'name' => 'John']);
        
        $this->userRepositoryMock
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($user);

        $result = $this->userService->getUserById(1);
        
        $this->assertEquals($user, $result);
    }

    public function test_create_user_hashes_password()
    {
        $inputData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ];

        $user = new User(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Jane Doe' &&
                       $data['email'] === 'jane@example.com' &&
                       Hash::check('password123', $data['password']);
            }))
            ->once()
            ->andReturn($user);

        $result = $this->userService->createUser($inputData);
        
        $this->assertEquals($user, $result);
    }

    public function test_update_user_hashes_password()
    {
        $inputData = [
            'name' => 'Updated Name',
            'password' => 'new_password',
        ];

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with(1, Mockery::on(function ($data) {
                return $data['name'] === 'Updated Name' &&
                       Hash::check('new_password', $data['password']);
            }))
            ->once()
            ->andReturn(true);

        $result = $this->userService->updateUser(1, $inputData);
        
        $this->assertTrue($result);
    }

    public function test_delete_user()
    {
        $this->userRepositoryMock
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);

        $result = $this->userService->deleteUser(1);
        
        $this->assertTrue($result);
    }

    public function test_get_user_by_email()
    {
        $user = new User(['id' => 1, 'email' => 'test@example.com']);
        
        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $result = $this->userService->getUserByEmail('test@example.com');
        
        $this->assertEquals($user, $result);
    }

    public function test_get_user_by_field()
    {
        $user = new User(['id' => 1, 'name' => 'Specific Name']);
        
        $this->userRepositoryMock
            ->shouldReceive('findByField')
            ->with('name', 'Specific Name')
            ->once()
            ->andReturn($user);

        $result = $this->userService->getUserByField('name', 'Specific Name');
        
        $this->assertEquals($user, $result);
    }

    public function test_count_users()
    {
        $this->userRepositoryMock
            ->shouldReceive('count')
            ->once()
            ->andReturn(10);

        $result = $this->userService->countUsers();
        
        $this->assertEquals(10, $result);
    }
}
