<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkNotificationsAsReadRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $notifications = Notification::with('user')
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Mark notifications as read.
     */
    public function markAsRead(MarkNotificationsAsReadRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $notificationIds = $validated['notification_ids'] ?? [];
        
        if (!empty($notificationIds)) {
            Notification::whereIn('id', $notificationIds)->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
        
        // Return updated notifications
        $notifications = Notification::with('user')
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }
}
