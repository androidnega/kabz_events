<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Display the support page.
     */
    public function index()
    {
        return view('client.support.index');
    }

    /**
     * Submit a support ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:technical,billing,general,complaint',
            'message' => 'required|string|min:10',
            'priority' => 'required|string|in:low,medium,high',
        ]);

        // Placeholder: In a real implementation, you would create a support ticket
        // SupportTicket::create([
        //     'user_id' => Auth::id(),
        //     'subject' => $request->subject,
        //     'category' => $request->category,
        //     'message' => $request->message,
        //     'priority' => $request->priority,
        //     'status' => 'open',
        // ]);

        return back()->with('success', 'Your support request has been submitted. We\'ll get back to you soon!');
    }
}

