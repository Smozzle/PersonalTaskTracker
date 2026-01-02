<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }

        // Redirect to the notification's URL if it exists
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();

        return response()->json([
            'count' => $count,
            'notifications' => Auth::user()
                ->unreadNotifications()
                ->take(5)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'message' => $notification->data['message'] ?? '',
                        'icon' => $notification->data['icon'] ?? 'fa-bell',
                        'color' => $notification->data['color'] ?? 'blue',
                        'url' => $notification->data['url'] ?? '#',
                        'time' => $notification->created_at->diffForHumans(),
                    ];
                })
        ]);
    }

    /**
     * Get latest notifications for dropdown (for AJAX)
     */
    public function getLatest()
    {
        $notifications = Auth::user()
            ->notifications()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'color' => $notification->data['color'] ?? 'blue',
                    'url' => $notification->data['url'] ?? '#',
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => $notification->read_at !== null,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications->count()
        ]);
    }
}