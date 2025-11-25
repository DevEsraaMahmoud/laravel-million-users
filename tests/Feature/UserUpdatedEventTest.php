<?php

namespace Tests\Feature;

use App\Events\UserUpdated;
use App\Listeners\SendUserUpdateNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserUpdatedEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test UserUpdated event is dispatched when user is updated.
     */
    public function test_user_updated_event_is_dispatched(): void
    {
        Event::fake();

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->update([
            'first_name' => 'Jane',
        ]);

        event(new UserUpdated($user));

        Event::assertDispatched(UserUpdated::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    /**
     * Test SendUserUpdateNotification listener creates notification.
     */
    public function test_listener_creates_notification(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $event = new UserUpdated($user);
        $listener = new SendUserUpdateNotification();
        $listener->handle($event);

        $this->assertDatabaseHas('user_activity_notifications', [
            'user_id' => $user->id,
            'type' => 'updated',
            'message' => "User {$user->first_name} {$user->last_name} has been updated.",
            'read' => false,
        ]);

        $notification = Notification::where('user_id', $user->id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('updated', $notification->type);
        $this->assertIsArray($notification->data);
        $this->assertEquals($user->id, $notification->data['user_id']);
        $this->assertEquals("{$user->first_name} {$user->last_name}", $notification->data['user_name']);
        $this->assertEquals($user->email, $notification->data['user_email']);
    }

    /**
     * Test listener logs the notification.
     */
    public function test_listener_logs_notification(): void
    {
        Log::spy();

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $event = new UserUpdated($user);
        $listener = new SendUserUpdateNotification();
        $listener->handle($event);

        Log::shouldHaveReceived('info')
            ->once()
            ->with("User updated: {$user->first_name} {$user->last_name} (ID: {$user->id}, Email: {$user->email})");
    }

    /**
     * Test notification data contains correct information.
     */
    public function test_notification_data_contains_correct_information(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $event = new UserUpdated($user);
        $listener = new SendUserUpdateNotification();
        $listener->handle($event);

        $notification = Notification::where('user_id', $user->id)->first();
        $this->assertIsArray($notification->data);
        $this->assertArrayHasKey('user_id', $notification->data);
        $this->assertArrayHasKey('user_name', $notification->data);
        $this->assertArrayHasKey('user_email', $notification->data);
        $this->assertArrayHasKey('updated_at', $notification->data);
        $this->assertEquals($user->id, $notification->data['user_id']);
        $this->assertEquals('John Doe', $notification->data['user_name']);
        $this->assertEquals('john@example.com', $notification->data['user_email']);
    }

    /**
     * Test multiple updates create multiple notifications.
     */
    public function test_multiple_updates_create_multiple_notifications(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $event1 = new UserUpdated($user);
        $listener = new SendUserUpdateNotification();
        $listener->handle($event1);

        $user->refresh();
        $user->update(['first_name' => 'Jane']);

        $event2 = new UserUpdated($user);
        $listener->handle($event2);

        $notifications = Notification::where('user_id', $user->id)->get();
        $this->assertCount(2, $notifications);
    }
}

