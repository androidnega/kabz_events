<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'vendor'])
            ->latest()
            ->paginate(15);
        
        return view('admin.reports.index', compact('reports'));
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
            'status' => 'open',
            'resolved_at' => null,
        ]);
        
        return back()->with('info', 'Report reopened.');
    }
}
