<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return paginated notifications for the authenticated user.
     * Used by the bell-icon dropdown (latest 10) and a full notifications page.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $notifications = $request->user()
                ->notifications()
                ->latest()
                ->paginate($request->integer('per_page', 10));

            return response()->json([
                'data'         => $notifications->items(),
                'unread_count' => $request->user()->unreadNotifications()->count(),
                'has_more'     => $notifications->hasMorePages(),
            ]);
        }

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, string $id)
    {
        $request->user()
            ->notifications()
            ->findOrFail($id)
            ->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Store or update the OneSignal player ID for the authenticated user.
     * Called by mobile apps after login.
     */
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'onesignal_player_id' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update([
            'onesignal_player_id' => $request->onesignal_player_id,
        ]);

        return response()->json(['success' => true]);
    }
}
