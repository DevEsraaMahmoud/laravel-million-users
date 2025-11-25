<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an address can be created.
     */
    public function test_address_can_be_created(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);
    }

    /**
     * Test that an address belongs to a user.
     */
    public function test_address_belongs_to_user(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);

        $this->assertInstanceOf(User::class, $address->user);
        $this->assertEquals($user->id, $address->user->id);
    }

    /**
     * Test that address can be updated.
     */
    public function test_address_can_be_updated(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);

        $address->update([
            'city' => 'Los Angeles',
            'post_code' => '90001',
        ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'city' => 'Los Angeles',
            'post_code' => '90001',
        ]);
    }
}

