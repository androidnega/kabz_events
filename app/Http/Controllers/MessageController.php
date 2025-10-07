<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a message from current user to vendor (vendor.user).
     * Request: vendor_id, message
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'message'   => 'required|string|max:3000',
        ]);

        // Spam protection: prevent links in messages
        if (preg_match('/(http:\/\/|https:\/\/|www\.)/i', $request->message)) {
            return response()->json(['error' => 'Links are not allowed in messages.'], 422);
        }

        $vendor = Vendor::findOrFail($request->vendor_id);
        $sender = Auth::user();
        $receiverUser = $vendor->user; // vendor owner user

        // Prevent self-messaging (vendor sending to their own vendor via client UI)
        if ($sender->id === $receiverUser->id) {
            return response()->json(['error' => 'Cannot message yourself.'], 422);
        }

        $msg = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiverUser->id,
            'vendor_id'   => $vendor->id,
            'message'     => $request->message,
            'from_vendor' => $sender->id === $vendor->user_id,
        ]);

        // Send notification to receiver
        try {
            $receiverUser->notify(new \App\Notifications\NewMessageNotification($msg));
        } catch (\Throwable $e) {
            \Log::warning('Message notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'ok' => true,
            'message' => $msg->fresh('sender')
        ]);
    }

    /**
     * List conversations for the authenticated user (grouped by vendor)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // For vendors, show conversations for vendors they own
        if ($user->hasRole('vendor')) {
            $vendor = $user->vendor;
            if (!$vendor) {
                return view('vendor.messages.index', ['conversations' => collect(), 'vendor' => null]);
            }

            // Get distinct other user ids who've messaged this vendor
            $conversations = Message::where('vendor_id', $vendor->id)
                ->select('sender_id', 'receiver_id', 'vendor_id')
                ->orderByDesc('created_at')
                ->get()
                ->groupBy(function ($m) use ($user, $vendor) {
                    // group by the counterparty user id (not the vendor owner)
                    return $m->sender_id === $user->id ? $m->receiver_id : $m->sender_id;
                });

            // flatten to conversation preview list: counterparty user + last message
            $preview = $conversations->map(function ($msgs, $counterpartyId) {
                $last = $msgs->sortByDesc('created_at')->first();
                return [
                    'counterparty' => User::find($counterpartyId),
                    'last_message' => $last,
                    'unread' => $msgs->whereNull('read_at')->where('receiver_id', auth()->id())->count()
                ];
            })->values();

            return view('vendor.messages.index', ['conversations' => $preview, 'vendor' => $vendor]);
        }

        // For clients: conversations they are part of (group by vendor)
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('vendor_id')
            ->map(function ($msgs, $vendorId) use ($user) {
                $last = $msgs->sortByDesc('created_at')->first();
                $vendor = Vendor::find($vendorId);
                $counterpartyId = $msgs->where('sender_id', '!=', $user->id)->first()->sender_id ?? $msgs->where('receiver_id', '!=', $user->id)->first()->receiver_id ?? null;
                return [
                    'vendor' => $vendor,
                    'last_message' => $last,
                    'unread' => $msgs->whereNull('read_at')->where('receiver_id', $user->id)->count(),
                ];
            })->values();

        return view('client.messages.index', ['conversations' => $conversations]);
    }

    /**
     * Return messages for a specific conversation (vendor + otherUser)
     * GET /messages/conversation?vendor_id=XX&user_id=YY
     */
    public function conversation(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'user_id'   => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $vendor = Vendor::findOrFail($request->vendor_id);
        $otherUser = User::findOrFail($request->user_id);

        // security: either authenticated user is buyer or vendor owner
        if (!($user->id === $otherUser->id || $user->id === $vendor->user_id || $otherUser->id === $vendor->user_id || $user->hasRole('admin') || $user->hasRole('superadmin'))) {
            abort(403);
        }

        $messages = Message::where('vendor_id', $vendor->id)
            ->where(function ($q) use ($user, $otherUser) {
                $q->where(function ($q2) use ($user, $otherUser) {
                    $q2->where('sender_id', $user->id)->where('receiver_id', $otherUser->id);
                })->orWhere(function ($q2) use ($user, $otherUser) {
                    $q2->where('sender_id', $otherUser->id)->where('receiver_id', $user->id);
                });
            })
            ->orderBy('created_at', 'asc')
            ->with(['sender', 'receiver'])
            ->get();

        // mark messages received by current user as read
        Message::where('vendor_id', $vendor->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }
}

