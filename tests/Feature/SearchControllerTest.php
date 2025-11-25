<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test users search endpoint returns JSON.
     */
    public function test_users_search_returns_json(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->getJson(route('search.users', ['query' => 'John']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'first_name', 'last_name', 'email', 'created_at'],
            ],
        ]);
    }

    /**
     * Test users search filters by query.
     */
    public function test_users_search_filters_by_query(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->getJson(route('search.users', ['query' => 'John']));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John', $data[0]['first_name']);
    }

    /**
     * Test users search respects limit parameter.
     */
    public function test_users_search_respects_limit(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            User::create([
                'first_name' => "User{$i}",
                'last_name' => "Test",
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        $response = $this->getJson(route('search.users', ['query' => '', 'limit' => 10]));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(10, $data);
    }

    /**
     * Test users search limits maximum results to 100.
     */
    public function test_users_search_limits_maximum_results(): void
    {
        for ($i = 1; $i <= 150; $i++) {
            User::create([
                'first_name' => "User{$i}",
                'last_name' => "Test",
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        $response = $this->getJson(route('search.users', ['query' => '', 'limit' => 200]));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertLessThanOrEqual(100, count($data));
    }

    /**
     * Test users search by email.
     */
    public function test_users_search_by_email(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->getJson(route('search.users', ['query' => 'john@example.com']));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('john@example.com', $data[0]['email']);
    }

    /**
     * Test users search by address.
     */
    public function test_users_search_by_address(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        Address::create([
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);

        $response = $this->getJson(route('search.users', ['query' => 'New York']));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John', $data[0]['first_name']);
    }

    /**
     * Test users search returns empty array when no results.
     */
    public function test_users_search_returns_empty_when_no_results(): void
    {
        $response = $this->getJson(route('search.users', ['query' => 'Nonexistent']));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }
}

