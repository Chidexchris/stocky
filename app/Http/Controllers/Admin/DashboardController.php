<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $total = User::count();
        $active = User::where('is_active', 1)->count();
        $inactive = User::where('is_active', 0)->count();
        $latest = User::orderByDesc('id')->limit(10)->get();
        return view('admin.dashboard', compact('total', 'active', 'inactive', 'latest'));
    }
}

