<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SecurityController extends Controller
{
    /**
     * Display a list of active user sessions across the platform.
     */
    public function sessions()
    {
        // Fetch sessions from the database session driver table
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('last_activity', 'desc')
            ->paginate(20);

        return view('superadmin.security.sessions', compact('sessions'));
    }

    /**
     * Terminate a specific user session.
     */
    public function terminateSession($id)
    {
        DB::table('sessions')->where('id', $id)->delete();

        return back()->with('success', 'User session terminated. The user will be logged out on their next request.');
    }

    /**
     * Terminate all sessions for a specific user.
     */
    public function terminateUserSessions($userId)
    {
        DB::table('sessions')->where('user_id', $userId)->delete();

        return back()->with('success', 'All sessions for this user have been terminated.');
    }
}
