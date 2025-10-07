<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SMSService;
use Illuminate\Http\Request;

class SMSTestController extends Controller
{
    /**
     * Show SMS test interface
     */
    public function index()
    {
        return view('superadmin.settings.sms_test');
    }

    /**
     * Send test SMS
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:160',
        ]);

        $sent = SMSService::send($request->phone, $request->message);

        if ($sent) {
            return back()->with('success', 'Test SMS sent successfully to ' . $request->phone . '! 🇬🇭');
        }

        return back()->with('error', 'Failed to send SMS. Check your Arkassel API configuration.');
    }
}
