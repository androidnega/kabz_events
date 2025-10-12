<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\UserOnlineStatus;
use App\Models\Vendor;
use App\Models\VendorResponseTime;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    private $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->middleware('auth');
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Show messages page.
     */
    public function index()
    {
        $vendor = Vendor::where('user_id', Auth::id())->firstOrFail();
        $vendorUserId = Auth::id();

        // Get all unique conversations
        $conversations = Message::where('vendor_id', $vendor->id)
            ->where(function ($query) use ($vendorUserId) {
                $query->where('sender_id', $vendorUserId)
                      ->where('deleted_by_sender', false)
                      ->orWhere(function ($q) use ($vendorUserId) {
                          $q->where('receiver_id', $vendorUserId)
                            ->where('deleted_by_receiver', false);
                      });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($vendorUserId) {
                return $message->sender_id === $vendorUserId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages, $clientId) use ($vendorUserId) {
                $latestMessage = $messages->first();
                $unreadCount = $messages->where('receiver_id', $vendorUserId)
                                       ->where('is_read', false)
                                       ->count();
                
                // Get client info
                $client = $messages->first()->sender_id === $vendorUserId 
                    ? $messages->first()->receiver 
                    : $messages->first()->sender;

                // Get client's online status
                $clientStatus = UserOnlineStatus::where('user_id', $clientId)->first();
                
                return [
                    'client' => $client,
                    'client_status' => [
                        'is_online' => $clientStatus?->is_online ?? false,
                        'last_seen' => $clientStatus?->lastSeenText() ?? 'Offline',
                    ],
                    'latest_message' => $latestMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values();

        return view('vendor.messages.index', compact('conversations', 'vendor'));
    }

    /**
     * Get conversation messages with a specific client.
     */
    public function getConversation(Request $request, $clientId)
    {
        $vendor = Vendor::where('user_id', Auth::id())->firstOrFail();
        $vendorUserId = Auth::id();

        $messages = Message::conversation($clientId, $vendorUserId, $vendor->id)
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
        Message::where('receiver_id', $vendorUserId)
            ->where('sender_id', $clientId)
            ->where('vendor_id', $vendor->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Get client online status
        $clientStatus = UserOnlineStatus::where('user_id', $clientId)->first();

        // Check if client is typing
        $isTyping = \App\Models\TypingIndicator::isUserTyping($clientId, Auth::id());

        return response()->json([
            'messages' => $messages,
            'client_status' => [
                'is_online' => $clientStatus?->is_online ?? false,
                'last_seen' => $clientStatus?->lastSeenText() ?? 'Offline',
            ],
            'is_typing' => $isTyping,
        ]);
    }

    /**
     * Send a message to client.
     */
    public function sendMessage(Request $request, $clientId)
    {
        $request->validate([
            'message' => 'nullable|string|required_without_all:image,audio',
            'image' => 'nullable|image|max:5120', // 5MB max
            'audio' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240', // 10MB max
        ]);

        $vendor = Vendor::where('user_id', Auth::id())->firstOrFail();
        $vendorUserId = Auth::id();

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
            'sender_id' => $vendorUserId,
            'sender_type' => 'vendor',
            'receiver_id' => $clientId,
            'receiver_type' => 'client',
            'vendor_id' => $vendor->id,
            'message' => $request->message,
            'media_type' => $mediaType,
            'media_url' => $mediaUrl,
        ]);

        // Calculate response time if this is a reply to client's message
        $lastClientMessage = Message::where('sender_id', $clientId)
            ->where('receiver_id', $vendorUserId)
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->first();

        if ($lastClientMessage) {
            $responseTimeMinutes = now()->diffInMinutes($lastClientMessage->created_at);
            
            VendorResponseTime::create([
                'vendor_id' => $vendor->id,
                'client_message_id' => $lastClientMessage->id,
                'vendor_reply_id' => $message->id,
                'response_time_minutes' => $responseTimeMinutes,
            ]);
        }

        // Load relationships
        $message->load(['sender', 'receiver']);

        // Create notification for client
        $this->createMessageNotification($clientId, Auth::id(), $vendor->id, $message);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Create notification for new message.
     */
    private function createMessageNotification($clientId, $vendorId, $vendorIdRecord, $message)
    {
        // Create notification in database
        \App\Models\Notification::create([
            'type' => 'message_received',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $clientId,
            'data' => [
                'message_id' => $message->id,
                'vendor_id' => $vendorIdRecord,
                'message_preview' => $message->message ? \Illuminate\Support\Str::limit($message->message, 50) : 'Sent a photo/audio',
                'sender_name' => Auth::user()->vendor->business_name ?? Auth::user()->name,
            ],
        ]);
    }

    /**
     * Handle typing indicator.
     */
    public function typing(Request $request, $clientId)
    {
        $vendor = Auth::user()->vendor;
        \App\Models\TypingIndicator::setTyping(Auth::id(), $clientId, $vendor->id);
        return response()->json(['success' => true]);
    }

    /**
     * Handle stop typing indicator.
     */
    public function stopTyping(Request $request, $clientId)
    {
        \App\Models\TypingIndicator::setNotTyping(Auth::id(), $clientId);
        return response()->json(['success' => true]);
    }

    /**
     * Delete a message.
     */
    public function deleteMessage($messageId)
    {
        $vendor = Vendor::where('user_id', Auth::id())->firstOrFail();
        $message = Message::where('vendor_id', $vendor->id)->findOrFail($messageId);
        $userId = Auth::id();

        // Check if user is sender or receiver
        if ($message->sender_id === $userId) {
            $message->update(['deleted_by_sender' => true]);
        } elseif ($message->receiver_id === $userId) {
            $message->update(['deleted_by_receiver' => true]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // If deleted by both, actually delete the record
        if ($message->deleted_by_sender && $message->deleted_by_receiver) {
            $message->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Update vendor's online status.
     */
    public function updateOnlineStatus(Request $request)
    {
        $userId = Auth::id();
        $isOnline = $request->input('is_online', true);

        UserOnlineStatus::updateStatus($userId, 'vendor', $isOnline);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
        ]);
    }
}

