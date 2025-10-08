<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReportSubmittedNotification;

class ReportController extends Controller
{
    /**
     * Display the report form
     */
    public function create()
    {
        return view('client.report');
    }

    /**
     * Store a new report (client/vendor creates report)
     * Rate limited: 5 reports per minute
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_id' => 'nullable|exists:users,id',
            'target_type' => 'required|in:vendor,client,service,other',
            'category' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Prevent users from reporting themselves
        if ($validated['target_id'] && $validated['target_id'] == auth()->id()) {
            return back()->with('error', 'You cannot report yourself.');
        }

        // Create the report
        $report = Report::create([
            'user_id' => auth()->id(),
            'target_id' => $validated['target_id'],
            'target_type' => $validated['target_type'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        // Notify admins about new report
        $this->notifyAdmins($report);

        return back()->with('success', 'Report submitted successfully! Our team will review it shortly. 🇬🇭');
    }

    /**
     * Notify all admins about a new report
     */
    private function notifyAdmins(Report $report)
    {
        try {
            // Get all admin and super-admin users
            $admins = User::role(['admin', 'super-admin'])->get();
            
            foreach ($admins as $admin) {
                // You can add email notification here if needed
                // $admin->notify(new ReportSubmittedNotification($report));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to notify admins about report: ' . $e->getMessage());
        }
    }
}

