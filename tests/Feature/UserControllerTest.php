<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test index page displays users.
     */
    public function test_index_displays_users(): void
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('users.data', 1)
        );
    }

    /**
     * Test index page with search query.
     */
    public function test_index_with_search_query(): void
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

        $response = $this->get(route('users.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('users.data', 1)
            ->where('search', 'John')
        );
    }

    /**
     * Test create page displays.
     */
    public function test_create_page_displays(): void
    {
        $response = $this->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Create')
        );
    }

    /**
     * Test store creates a new user with address.
     */
    public function test_store_creates_user_with_address(): void
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => [
                'country' => 'USA',
                'city' => 'New York',
                'post_code' => '10001',
                'street' => '123 Main St',
            ],
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
            'post_code' => '10001',
            'street' => '123 Main St',
        ]);
    }

    /**
     * Test store validates required fields.
     */
    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('users.store'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'address.country', 'address.city', 'address.post_code', 'address.street']);
    }

    /**
     * Test store validates unique email.
     */
    public function test_store_validates_unique_email(): void
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => [
                'country' => 'USA',
                'city' => 'New York',
                'post_code' => '10001',
                'street' => '123 Main St',
            ],
        ];

        $response = $this->post(route('users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test show displays user details.
     */
    public function test_show_displays_user_details(): void
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

        $response = $this->get(route('users.show', $user));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Show')
            ->where('user.id', $user->id)
        );
    }

    /**
     * Test show returns JSON for AJAX requests.
     */
    public function test_show_returns_json_for_ajax_requests(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->getJson(route('users.show', $user));

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
            ],
        ]);
    }

    /**
     * Test edit page displays.
     */
    public function test_edit_page_displays(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get(route('users.edit', $user));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Edit')
            ->where('user.id', $user->id)
        );
    }

    /**
     * Test update modifies user and address.
     */
    public function test_update_modifies_user_and_address(): void
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

        Event::fake();

        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'address' => [
                'country' => 'Canada',
                'city' => 'Toronto',
                'post_code' => 'M5H 2N2',
                'street' => '456 Queen St',
            ],
        ];

        $response = $this->put(route('users.update', $user), $userData);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'country' => 'Canada',
            'city' => 'Toronto',
            'post_code' => 'M5H 2N2',
            'street' => '456 Queen St',
        ]);

        Event::assertDispatched(\App\Events\UserUpdated::class);
    }

    /**
     * Test update creates address if it doesn't exist.
     */
    public function test_update_creates_address_if_not_exists(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'address' => [
                'country' => 'USA',
                'city' => 'New York',
                'post_code' => '10001',
                'street' => '123 Main St',
            ],
        ];

        $response = $this->put(route('users.update', $user), $userData);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'country' => 'USA',
            'city' => 'New York',
        ]);
    }

    /**
     * Test destroy deletes user.
     */
    public function test_destroy_deletes_user(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test index displays unread notifications.
     */
    public function test_index_displays_unread_notifications(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'updated',
            'message' => 'User updated',
            'data' => [],
            'read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'updated',
            'message' => 'User updated (read)',
            'data' => [],
            'read' => true,
        ]);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('notifications', 1)
        );
    }
}

