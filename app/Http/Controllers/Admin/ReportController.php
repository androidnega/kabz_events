<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'vendor', 'target', 'reporter']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        // Filter by target_type
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // Search in message or category
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('message', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->latest()->paginate(15)->appends(request()->query());
        
        // Get unique categories for filter dropdown
        $categories = Report::distinct()->pluck('category')->filter();
        
        return view('admin.reports.index', compact('reports', 'categories'));
    }

    /**
     * Show detailed view of a specific report
     */
    public function show($id)
    {
        $report = Report::with(['user', 'vendor', 'target', 'reporter'])->findOrFail($id);
        
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update report status (pending -> in_review -> resolved)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_review,resolved',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $report = Report::findOrFail($id);
        
        $report->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note ?? $report->admin_note,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        // Notify reporter if resolved
        if ($request->status === 'resolved') {
            $this->notifyReporter($report);
        }

        $statusMessage = [
            'pending' => 'Report marked as pending',
            'in_review' => 'Report marked as in review 🔍',
            'resolved' => 'Report marked as resolved! 🇬🇭',
        ];

        return back()->with('success', $statusMessage[$request->status]);
    }

    /**
     * Store a new report (public endpoint for users to report vendors)
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'category' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'vendor_id' => $request->vendor_id,
            'type' => 'vendor',
            'category' => $request->category,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Report submitted successfully. Our team will review it shortly.'
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $report->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'admin_response' => $request->input('admin_response', 'Issue resolved by admin'),
        ]);
        
        return back()->with('success', 'Report marked as resolved! 🇬🇭');
    }

    public function reopen($id)
    {
        $report = Report::findOrFail($id);
        
        $report->update([
            'status' => 'pending',
            'resolved_at' => null,
        ]);
        
        return back()->with('info', 'Report reopened.');
    }

    /**
     * Notify reporter that their report has been resolved
     */
    private function notifyReporter(Report $report)
    {
        try {
            // You can add email/SMS notification here
            // $report->reporter->notify(new ReportResolvedNotification($report));
        } catch (\Exception $e) {
            Log::error('Failed to notify reporter about resolution: ' . $e->getMessage());
        }
    }
}
