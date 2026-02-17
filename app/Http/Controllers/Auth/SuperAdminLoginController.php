<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/platform/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('superadmin.auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->hasRole('Super Admin')) {
            Auth::logout();
            return back()->withErrors([
                'email' => trans('auth.failed'),
            ]);
        }

        return redirect()->intended($this->redirectTo);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }
}
