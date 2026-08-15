<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        Log::info('Login form displayed', [
            'session_id' => session()->getId(),
            'session_token' => session()->token()
        ]);
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Login attempt started', [
            'email' => $request->email,
            'session_id' => session()->getId(),
            'ip' => $request->ip(),
            'has_token' => $request->has('_token'),
        ]);

        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            Log::info('Login successful', [
                'email' => $request->email,
                'user_id' => Auth::id()
            ]);
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        Log::warning('Login failed', ['email' => $request->email]);

        return $this->sendFailedLoginResponse($request);
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'is_active' => true,
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->hasVerifiedEmail()) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('activation.notice', ['email' => $user->email])
                ->with('status', 'Please activate your account before signing in.');
        }

        return null;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        Log::error('Login failed response sent', ['email' => $request->email]);
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    public function logout(Request $request)
    {
        Log::info('User logged out', ['user_id' => Auth::id()]);
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
