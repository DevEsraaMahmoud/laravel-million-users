<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use App\Services\UserSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserSearchService();
        Cache::flush();
    }

    /**
     * Test paginated search returns users.
     */
    public function test_paginated_search_returns_users(): void
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

        $result = $this->service->paginated(null, 15);

        $this->assertCount(2, $result->items());
        $this->assertEquals(2, $result->total());
    }

    /**
     * Test paginated search with query filters results.
     */
    public function test_paginated_search_with_query_filters_results(): void
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

        $result = $this->service->paginated('John', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('John', $result->items()[0]->first_name);
    }

    /**
     * Test search by email.
     */
    public function test_search_by_email(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $result = $this->service->paginated('john@example.com', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('john@example.com', $result->items()[0]->email);
    }

    /**
     * Test search by address.
     */
    public function test_search_by_address(): void
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

        $result = $this->service->paginated('New York', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('John', $result->items()[0]->first_name);
    }

    /**
     * Test search collection returns limited results.
     */
    public function test_search_collection_returns_limited_results(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            User::create([
                'first_name' => "User{$i}",
                'last_name' => "Test",
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        $result = $this->service->searchCollection(null, 20);

        $this->assertCount(20, $result);
    }

    /**
     * Test search results are cached.
     */
    public function test_search_results_are_cached(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->service->paginated('John', 15);
    }

    /**
     * Test chunk processes users in batches.
     */
    public function test_chunk_processes_users_in_batches(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            User::create([
                'first_name' => "User{$i}",
                'last_name' => "Test",
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        $chunkCount = 0;
        $totalUsers = 0;

        $this->service->chunk(function ($users) use (&$chunkCount, &$totalUsers) {
            $chunkCount++;
            $totalUsers += $users->count();
        }, 10);

        $this->assertEquals(3, $chunkCount); // 25 users / 10 per chunk = 3 chunks
        $this->assertEquals(25, $totalUsers);
    }

    /**
     * Test cursor iterates through users.
     */
    public function test_cursor_iterates_through_users(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'first_name' => "User{$i}",
                'last_name' => "Test",
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
        }

        $count = 0;
        foreach ($this->service->cursor() as $user) {
            $count++;
            $this->assertInstanceOf(User::class, $user);
        }

        $this->assertEquals(5, $count);
    }

    /**
     * Test base query includes address relationship.
     */
    public function test_base_query_includes_address_relationship(): void
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

        $result = $this->service->paginated(null, 15);

        $this->assertNotNull($result->items()[0]->address);
        $this->assertEquals('USA', $result->items()[0]->address->country);
    }
}

