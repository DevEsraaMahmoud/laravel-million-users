<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Http\Requests\MarkNotificationsAsReadRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $notifications = CacheHelper::rememberWithTags(
                ['notifications'],
                'notifications:unread:limit_10',
                30,
                function () {
                    return Notification::with('user')
                        ->where('read', false)
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                }
            );
        
            return response()->json([
                'notifications' => NotificationResource::collection($notifications),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to load notifications.',
            ], 500);
        }
    }

    /**
     * Mark notifications as read.
     */
    public function markAsRead(MarkNotificationsAsReadRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $notificationIds = $validated['notification_ids'] ?? [];
            
            if (!empty($notificationIds)) {
                Notification::whereIn('id', $notificationIds)->update([
                    'read' => true,
                    'read_at' => now(),
                ]);

                // Clear notifications cache
                CacheHelper::flushTags(['notifications']);
            }
            
            // Return updated notifications
            $notifications = Notification::with('user')
                ->where('read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'notifications' => NotificationResource::collection($notifications),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notifications as read', [
                'notification_ids' => $notificationIds ?? [],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update notifications.',
            ], 500);
        }
    }
}
