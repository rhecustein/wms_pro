<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in notification data (JSON search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('data', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest('created_at')->paginate(20);

        // Get statistics
        $stats = [
            'total' => Auth::user()->notifications()->count(),
            'unread' => Auth::user()->unreadNotifications()->count(),
            'read' => Auth::user()->notifications()->whereNotNull('read_at')->count(),
            'today' => Auth::user()->notifications()->whereDate('created_at', today())->count(),
        ];

        // Get notification types for filter (Fixed query)
        $types = DB::table('notifications')
            ->where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->select('type')
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->type,
                    'label' => $this->formatNotificationType($item->type)
                ];
            })
            ->toArray();

        return view('system.notifications.index', compact('notifications', 'stats', 'types'));
    }

    /**
     * Display notification detail
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark as read when viewing
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // Get related notifications (same type)
        $relatedNotifications = Auth::user()->notifications()
            ->where('type', $notification->type)
            ->where('id', '!=', $notification->id)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('system.notifications.show', compact('notification', 'relatedNotifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
            return redirect()->back()->with('success', 'Notification marked as read');
        }

        return redirect()->back()->with('info', 'Notification already marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $count = Auth::user()->unreadNotifications->count();
        
        if ($count > 0) {
            Auth::user()->unreadNotifications->markAsRead();
            return redirect()->back()->with('success', "{$count} notification(s) marked as read");
        }

        return redirect()->back()->with('info', 'No unread notifications');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Notification deleted successfully']);
        }

        return redirect()->route('system.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        $count = Auth::user()->notifications()
            ->whereNotNull('read_at')
            ->count();

        if ($count > 0) {
            Auth::user()->notifications()
                ->whereNotNull('read_at')
                ->delete();

            return redirect()->back()->with('success', "{$count} read notification(s) deleted successfully");
        }

        return redirect()->back()->with('info', 'No read notifications to delete');
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Format notification type for display
     */
    private function formatNotificationType($type)
    {
        // Remove namespace
        $parts = explode('\\', $type);
        $className = end($parts);
        
        // Remove "Notification" suffix
        $className = str_replace('Notification', '', $className);
        
        // Convert PascalCase to Title Case
        return preg_replace('/(?<!^)([A-Z])/', ' $1', $className);
    }
}