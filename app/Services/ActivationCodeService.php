<?php

namespace App\Services;

use App\Mail\ActivationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ActivationCodeService
{
    public function generateFor(User $user): string
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'activation_code_hash' => Hash::make($code),
            'activation_expires_at' => now()->addMinutes(30),
        ])->save();

        return $code;
    }

    public function send(User $user): bool
    {
        $code = $this->generateFor($user);

        if ($this->phaseOneDemo()) {
            Log::info('Activation email skipped in phase-one demo mode; showing demo code instead.', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            session()->flash('activation_demo_code', $code);
            session()->flash('status', 'Email delivery is not connected on this demo environment. Use the demo activation code shown below.');

            return false;
        }

        try {
            Mail::to($user->email, $user->name)->send(new ActivationCodeMail($user, $code));

            return true;
        } catch (\Throwable $e) {
            if (! $this->phaseOneDemo()) {
                throw $e;
            }

            Log::warning('Activation email unavailable; showing demo code instead.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            session()->flash('activation_demo_code', $code);
            session()->flash('status', 'Email delivery is unavailable on this demo environment. Use the demo activation code shown below.');

            return false;
        }
    }

    private function phaseOneDemo(): bool
    {
        return filter_var(env('PHASE_ONE_DEMO', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function verify(User $user, string $code): bool
    {
        if (!$user->activation_code_hash || !$user->activation_expires_at) {
            return false;
        }

        if ($user->activation_expires_at->isPast()) {
            return false;
        }

        return Hash::check($code, $user->activation_code_hash);
    }
}
