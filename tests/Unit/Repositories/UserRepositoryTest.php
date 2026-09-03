<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User());
    }

    public function test_all_returns_collection_of_users()
    {
        User::factory()->count(3)->create();
        $users = $this->repository->all();
        $this->assertCount(3, $users);
    }

    public function test_find_returns_user()
    {
        $user = User::factory()->create();
        $foundUser = $this->repository->find($user->id);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_create_saves_new_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ];
        
        $user = $this->repository->create($data);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function test_update_modifies_existing_user()
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        
        $result = $this->repository->update($user->id, ['name' => 'New Name']);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_delete_removes_user()
    {
        $user = User::factory()->create();
        
        $result = $this->repository->delete($user->id);
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_by_email_returns_user()
    {
        $user = User::factory()->create(['email' => 'unique@example.com']);
        
        $found = $this->repository->findByEmail('unique@example.com');
        
        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_find_by_field_returns_user()
    {
        $user = User::factory()->create(['name' => 'Specific Name']);
        
        $found = $this->repository->findByField('name', 'Specific Name');
        
        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_count_returns_correct_number()
    {
        User::factory()->count(5)->create();
        
        $count = $this->repository->count();
        
        $this->assertEquals(5, $count);
    }
}
