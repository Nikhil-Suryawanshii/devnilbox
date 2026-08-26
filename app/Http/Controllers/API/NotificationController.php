<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    // notification list
    public function index()
    {
        $notifications = NotificationRepository::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(20);

        $unread = NotificationRepository::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'total_unread' => $unread,
            'notifications' => $notifications,
        ]);
    }

    // unread count
    public function unreadCount()
    {
        $count = NotificationRepository::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_notifications' => $count,
        ]);
    }

    // mark single as read
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    // mark all as read
    public function markAllAsRead()
    {
        NotificationRepository::query()
            ->where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    // delete notification
    public function destroy(Notification $notification)
    {
        if ($notification->user_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }
}