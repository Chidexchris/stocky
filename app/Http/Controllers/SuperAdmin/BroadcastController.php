<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SystemAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BroadcastController extends Controller
{
    public function create()
    {
        return view('superadmin.broadcasts.create');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Target all Business Owners
        $users = User::role('Business Owner')->get();

        Notification::send($users, new SystemAnnouncement($request->title, $request->message));

        return back()->with('success', 'Broadcast sent successfully to ' . $users->count() . ' business owners.');
    }
}
