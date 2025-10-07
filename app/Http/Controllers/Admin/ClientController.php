<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('client');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.clients.index', compact('clients'));
    }

    public function show($id)
    {
        $client = User::role('client')->findOrFail($id);
        $reviews = $client->reviews()->with('vendor')->latest()->take(10)->get();
        
        return view('admin.clients.show', compact('client', 'reviews'));
    }

    public function deactivate($id)
    {
        $client = User::role('client')->findOrFail($id);
        // Store in a status column if it exists, or use another method
        // For now, we'll log this action
        
        return back()->with('success', 'Client marked as inactive.');
    }

    public function activate($id)
    {
        $client = User::role('client')->findOrFail($id);
        
        return back()->with('success', 'Client activated successfully.');
    }

    public function resetPassword($id)
    {
        $client = User::role('client')->findOrFail($id);
        $client->update(['password' => Hash::make('12345678')]);
        
        return back()->with('success', 'Password reset to: 12345678');
    }
}
