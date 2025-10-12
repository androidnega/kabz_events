<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the authenticated user.
     */
    public function getUnreadNotifications()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $data,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        // Get unread message count
        $unreadMessageCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_message_count' => $unreadMessageCount,
            'total_unread' => $notifications->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get notification redirect URL.
     */
    public function getRedirectUrl($notificationId)
    {
        $notification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', Auth::id())
            ->findOrFail($notificationId);

        $data = json_decode($notification->data, true);
        $url = '#';

        switch ($notification->type) {
            case 'message_received':
                if (Auth::user()->hasRole('vendor')) {
                    $url = route('vendor.messages');
                } else {
                    $url = route('client.conversations');
                }
                break;
            
            case 'verification_approved':
                $url = route('vendor.verification');
                break;
                
            case 'verification_rejected':
                $url = route('vendor.verification');
                break;
                
            default:
                $url = route('dashboard');
        }

        return response()->json(['url' => $url]);
    }

    /**
     * Create a notification.
     */
    public static function create($userId, $type, $data)
    {
        return Notification::create([
            'type' => $type,
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => json_encode($data),
        ]);
    }

    /**
     * Send push notification (placeholder for future implementation).
     */
    public function sendPushNotification($userId, $title, $body, $data = [])
    {
        // This is a placeholder for push notification implementation
        // You can integrate with services like:
        // - Firebase Cloud Messaging (FCM)
        // - OneSignal
        // - Pusher Beams
        // - Apple Push Notification Service (APNs)
        
        // For now, we'll just create a database notification
        self::create($userId, 'push_notification', [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        return true;
    }
}