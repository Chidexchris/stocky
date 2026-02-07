<?php

namespace Modules\User\Http\Controllers;

use App\Models\LoginLog;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class LoginLogsController extends Controller
{
    public function index(Request $request) {
        abort_if(Gate::denies('access_user_management'), 403);

        $query = LoginLog::with(['user', 'store'])->orderByDesc('created_at');

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->get('store_id'));
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->get('event_type'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->paginate(50)->appends($request->query());
        $stores = Store::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('user::logs.index', compact('logs', 'stores', 'users'));
    }
}
