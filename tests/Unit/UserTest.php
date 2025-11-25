<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can be created.
     */
    public function test_user_can_be_created(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test that a user has one address.
     */
    public function test_user_has_one_address(): void
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

        $this->assertInstanceOf(Address::class, $user->address);
        $this->assertEquals($address->id, $user->address->id);
    }

    /**
     * Test the name accessor returns full name.
     */
    public function test_name_accessor_returns_full_name(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertEquals('John Doe', $user->name);
    }

    /**
     * Test that password is hashed when set.
     */
    public function test_password_is_hashed(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'plainpassword',
        ]);

        $this->assertNotEquals('plainpassword', $user->password);
        $this->assertTrue(password_verify('plainpassword', $user->password));
    }

    /**
     * Test that user can be created without address.
     */
    public function test_user_can_exist_without_address(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertNull($user->address);
    }
}

