<?php
// tests/Feature/UserTest.php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Currency;
use App\Repositories\UserRepository;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        Country::truncate();
        $this->userRepository = new UserRepository(new User());
    }

    public function testFilterUsersByCountry()
    {
        // داده‌های نمونه ایجاد کنید
        $country = Country::factory()->create(['code' => 'IN']);
        User::factory()->create(['country_id' => $country->id]);

        // فراخوانی متد و بررسی نتیجه
        $result = $this->userRepository->getAllUsers(['country' => 'IN']);
        $this->assertCount(1, $result);
    }

    public function testCreateUserViaApi()
    {
        // ایجاد کشور و ارز مرتبط
        $country = Country::factory()->create();
        $currency = Currency::factory()->create(['country_id' => $country->id]);

        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
        ]);

        $response->assertStatus(201)
            ->assertJson(['name' => 'Test User']);
    }

    public function testShowUserViaApi()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $user->id, 'name' => $user->name]);
    }

    public function testFilterUsersByCountryViaApi()
    {
        $country = Country::factory()->create(['code' => 'IR']);
        User::factory()->count(2)->create(['country_id' => $country->id]);

        $response = $this->getJson('/api/users?country=IR');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function testUpdateUserViaApi()
    {
        // ایجاد کاربر و اطلاعات اولیه
        $country = Country::factory()->create();
        $currency = Currency::factory()->create(['country_id' => $country->id]);
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
        ]);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'country_id' => $country->id,
            'currency_id' => $currency->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => 'Updated Name']);
    }

    public function testDeleteUserViaApi()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

}
