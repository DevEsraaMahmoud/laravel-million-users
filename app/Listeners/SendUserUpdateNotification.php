<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Helpers\CacheHelper;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserUpdateNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

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
        try {
            $user = $event->user;
            
            // Log the notification with structured data
            Log::info('User updated', [
                'user_id' => $user->id,
                'user_name' => "{$user->first_name} {$user->last_name}",
                'user_email' => $user->email,
                'updated_at' => $user->updated_at->toIso8601String(),
            ]);
            
            // Store notification in database
            $notification = Notification::create([
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

            // Invalidate notifications cache
            CacheHelper::flushTags(['notifications', "notifications.user.{$user->id}"]);
            
            // You can add more notification logic here:
            // - Send email notification
            // - Send push notification via broadcasting
            // - Update activity log
            // - Notify admins, etc.
        } catch (\Exception $e) {
            Log::error('Failed to send user update notification', [
                'user_id' => $event->user->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Re-throw to allow queue retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserUpdated $event, \Throwable $exception): void
    {
        Log::critical('User update notification job failed after all retries', [
            'user_id' => $event->user->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
