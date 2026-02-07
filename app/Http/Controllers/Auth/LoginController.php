<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/backend/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // ✅ status check
        if ($user->status != 1) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Your account is temporarily deactivated. Please contact your admin.');
        }
        $intended = $request->session()->get('url.intended');
        $badIntended = [
            null,
            '',
            url('/home'),
            url('/logout'),
            url('/login'),
            url('/'), // অনেক সময় root কে intended ধরে রাখে
        ];

        // যদি intended আপনার অ্যাপের বাইরে যায়, এটাও ignore
        $isExternal = $intended && !str_starts_with($intended, url('/'));

        if (in_array($intended, $badIntended, true) || $isExternal) {
            $request->session()->forget('url.intended');
            return redirect($this->redirectTo);
        }

        // ✅ intended valid হলে যাক, না হলে dashboard
        return redirect()->intended($this->redirectTo);
    }
}
