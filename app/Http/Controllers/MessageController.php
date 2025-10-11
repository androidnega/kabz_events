<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\UserOnlineStatus;
use App\Models\Vendor;
use App\Models\VendorResponseTime;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    private $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->middleware('auth');
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Get conversation messages between client and vendor.
     */
    public function getConversation(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $clientId = Auth::id();
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
        
        // Get average response time
        $averageResponseTime = VendorResponseTime::getAverageResponseTime($vendorId);

        return response()->json([
            'messages' => $messages,
            'vendor_status' => [
                'is_online' => $vendorStatus?->is_online ?? false,
                'last_seen' => $vendorStatus?->lastSeenText() ?? 'Offline',
                'average_response_time' => $averageResponseTime,
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

        // Broadcast event (will implement later)
        // broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete a message.
     */
    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);
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
     * Get all conversations for the authenticated client.
     */
    public function getConversations()
    {
        $clientId = Auth::id();

        $conversations = Message::where(function ($query) use ($clientId) {
            $query->where('sender_id', $clientId)
                  ->orWhere('receiver_id', $clientId);
        })
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

        return response()->json($conversations);
    }

    /**
     * Update user's online status.
     */
    public function updateOnlineStatus(Request $request)
    {
        $userId = Auth::id();
        $isOnline = $request->input('is_online', true);

        UserOnlineStatus::updateStatus($userId, 'client', $isOnline);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
        ]);
    }
}
