<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InAppNotification;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user's role
     */
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        
        $notifications = InAppNotification::forRole($role)
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'icon' => $n->icon,
                'color' => $n->color,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
                'created_at' => $n->created_at->format('d/m/Y H:i'),
            ]);

        $unreadCount = InAppNotification::forRole($role)->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(InAppNotification $notification)
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for current role
     */
    public function markAllRead()
    {
        $role = auth()->user()->role;

        InAppNotification::forRole($role)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah dibaca.']);
    }
}
