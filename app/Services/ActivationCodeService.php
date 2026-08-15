<?php

namespace App\Services;

use App\Mail\ActivationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    public function send(User $user): void
    {
        $code = $this->generateFor($user);

        Mail::to($user->email, $user->name)->send(new ActivationCodeMail($user, $code));
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
