<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivationCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    public function show(Request $request)
    {
        return view('auth.activate', [
            'email' => $request->input('email', Auth::user()->email ?? ''),
        ]);
    }

    public function activate(Request $request, ActivationCodeService $activationCodes)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'activation_code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !$activationCodes->verify($user, $data['activation_code'])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['activation_code' => 'The activation code is invalid or expired.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'activation_code_hash' => null,
            'activation_expires_at' => null,
            'is_active' => true,
        ])->save();

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Your account has been activated.');
    }

    public function resend(Request $request, ActivationCodeService $activationCodes)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $activationCodes->send($user);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('status', 'If the account exists and is not active, a new activation code has been sent.');
    }
}
