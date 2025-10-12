<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\UserOnlineStatus;
use App\Models\Vendor;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    private $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->middleware('auth');
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Show client messages page.
     */
    public function index()
    {
        $clientId = Auth::id();

        // Get all unique conversations for this client
        $conversations = Message::where(function ($query) use ($clientId) {
            $query->where('sender_id', $clientId)
                  ->orWhere('receiver_id', $clientId);
        })
        ->where('vendor_id', '!=', null)
        ->with(['sender', 'receiver', 'vendor'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('vendor_id')
        ->map(function ($messages) use ($clientId) {
            $latestMessage = $messages->first();
            $unreadCount = $messages->where('receiver_id', $clientId)
                                   ->where('is_read', false)
                                   ->count();
            
            return [
                'vendor' => $latestMessage->vendor,
                'latest_message' => $latestMessage,
                'unread_count' => $unreadCount,
            ];
        })
        ->values();

        return view('client.messages.index', compact('conversations'));
    }

    /**
     * Get conversation messages with a specific vendor.
     */
    public function getConversation(Request $request, $vendorId)
    {
        $clientId = Auth::id();
        $vendor = Vendor::findOrFail($vendorId);
        $vendorUserId = $vendor->user_id;

        $messages = Message::conversation($clientId, $vendorUserId, $vendorId)
            ->where(function ($query) use ($clientId, $vendorUserId) {
                $query->where(function ($q) use ($clientId) {
                    $q->where('sender_id', $clientId)->where('deleted_by_sender', false);
                })->orWhere(function ($q) use ($vendorUserId) {
                    $q->where('sender_id', $vendorUserId)->where('deleted_by_receiver', false);
                });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', $clientId)
            ->where('sender_id', $vendorUserId)
            ->where('vendor_id', $vendorId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Get vendor online status
        $vendorStatus = UserOnlineStatus::where('user_id', $vendorUserId)->first();

        return response()->json([
            'messages' => $messages,
            'vendor_status' => [
                'is_online' => $vendorStatus?->is_online ?? false,
                'last_seen' => $vendorStatus?->lastSeenText() ?? 'Offline',
            ],
        ]);
    }

    /**
     * Send a message to vendor.
     */
    public function sendMessage(Request $request, $vendorId)
    {
        $request->validate([
            'message' => 'nullable|string|required_without_all:image,audio',
            'image' => 'nullable|image|max:5120', // 5MB max
            'audio' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240', // 10MB max
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        $clientId = Auth::id();
        $vendorUserId = $vendor->user_id;

        $mediaType = null;
        $mediaUrl = null;

        // Handle image upload
        if ($request->hasFile('image')) {
            $uploadResult = $this->cloudinaryService->uploadImage($request->file('image'), 'chat/images');
            if ($uploadResult) {
                $mediaType = 'image';
                $mediaUrl = $uploadResult['url'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image. Please try again.',
                ], 500);
            }
        }

        // Handle audio upload
        if ($request->hasFile('audio')) {
            $uploadResult = $this->cloudinaryService->uploadAudio($request->file('audio'), 'chat/audio');
            if ($uploadResult) {
                $mediaType = 'audio';
                $mediaUrl = $uploadResult['url'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload audio. Please try again.',
                ], 500);
            }
        }

        $message = Message::create([
            'sender_id' => $clientId,
            'sender_type' => 'client',
            'receiver_id' => $vendorUserId,
            'receiver_type' => 'vendor',
            'vendor_id' => $vendorId,
            'message' => $request->message,
            'media_type' => $mediaType,
            'media_url' => $mediaUrl,
        ]);

        // Load relationships
        $message->load(['sender', 'receiver']);

        // Create notification for vendor
        $this->createMessageNotification($vendorUserId, $clientId, $vendorId, $message);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Handle typing indicator.
     */
    public function typing(Request $request, $vendorId)
    {
        // Store typing status (you can implement this with Redis or database)
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }

    /**
     * Handle stop typing indicator.
     */
    public function stopTyping(Request $request, $vendorId)
    {
        // Clear typing status
        return response()->json(['success' => true]);
    }

    /**
     * Create notification for new message.
     */
    private function createMessageNotification($vendorUserId, $clientId, $vendorId, $message)
    {
        // Create notification in database
        Notification::create([
            'type' => 'message_received',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $vendorUserId,
            'data' => [
                'message_id' => $message->id,
                'client_id' => $clientId,
                'vendor_id' => $vendorId,
                'message_preview' => $message->message ? Str::limit($message->message, 50) : 'Sent a photo/audio',
                'sender_name' => Auth::user()->name,
            ],
        ]);

        // Send push notification (implement based on your needs)
        // $this->sendPushNotification($vendorUserId, $message);
    }

    /**
     * Send push notification.
     */
    private function sendPushNotification($userId, $message)
    {
        // Implement push notification logic here
        // This could use Firebase, OneSignal, or any other service
    }
}
