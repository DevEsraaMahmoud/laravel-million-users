<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SendUserUpdateNotification
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserUpdated $event): void
    {
        $user = $event->user;
        
        // Log the notification
        Log::info("User updated: {$user->first_name} {$user->last_name} (ID: {$user->id}, Email: {$user->email})");
        
        // Store notification in database
        Notification::create([
            'user_id' => $user->id,
            'type' => 'updated',
            'message' => "User {$user->first_name} {$user->last_name} has been updated.",
            'data' => [
                'user_id' => $user->id,
                'user_name' => "{$user->first_name} {$user->last_name}",
                'user_email' => $user->email,
                'updated_at' => $user->updated_at->toIso8601String(),
            ],
            'read' => false,
        ]);
        
        // You can add more notification logic here:
        // - Send email notification
        // - Send push notification via broadcasting
        // - Update activity log
        // - Notify admins, etc.
    }
}
