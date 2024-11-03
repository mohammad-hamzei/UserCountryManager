<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\Currency;
use App\Models\User;
use App\Repositories\CountryRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Country::truncate();
        $this->userRepository = new UserRepository(new User());
    }

    public function testCreateUser()
    {
        $userRepo = new UserRepository(new User());

        $country = Country::factory()->create(['code' => 'IR']);
        $currency = Currency::factory()->create(['country_id' => $country->id, 'code' => 'IRR']);

        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
        ];

        $user = $userRepo->create($userData);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('testuser@example.com', $user->email);
    }

    public function testGetUserById()
    {
        $userRepo = new UserRepository(new User());

        $user = User::factory()->create();

        $foundUser = $userRepo->getById($user->id);

        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testFilterUsersByCurrency()
    {
        $country = Country::factory()->create(['code' => 'IN']);
        $currency = Currency::factory()->create(['country_id' => $country->id, 'code' => 'INR']);

        User::factory()->count(3)->create(['country_id' => $country->id, 'currency_id' => $currency->id]);

        $userRepo = new UserRepository(new User());
        $result = $userRepo->getAllUsers(['currency' => $currency->code]);

        $this->assertCount(3, $result);
    }

    public function testUpdateUser()
    {
        $userRepo = new UserRepository(new User());

        $user = User::factory()->create(['name' => 'Old Name']);
        $updatedData = ['name' => 'New Name'];

        $userRepo->update($user->id, $updatedData);

        $this->assertEquals('New Name', $user->fresh()->name);
    }

    public function testDeleteUser()
    {
        $userRepo = new UserRepository(new User());

        $user = User::factory()->create();

        $userRepo->delete($user->id);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
