@extends('emails.layout')

@section('content')
@php
    $recipientName = $user->name ?? $name ?? 'Valued User';
    $targetUrl = $resetUrl ?? url('/password/reset?token=' . ($token ?? ''));
    $expiry = $expiresInMinutes ?? 60;
@endphp
<div class="animate-fade">
    <div style="margin-bottom: 24px; text-align: center;">
        <div style="display: inline-block; background: #fef3c7; border: 1px solid #fde68a; border-radius: 50%; width: 56px; height: 56px; line-height: 56px; font-size: 28px; margin-bottom: 12px;">
            🔐
        </div>
        <h2 style="color: #1f2937; margin: 0 0 6px; font-size: 22px;">Reset Your KTM-WDC Password</h2>
        <p style="color: #6b7280; font-size: 14px; margin: 0;">Namaste {{ $recipientName }}, we received a request to reset your password.</p>
    </div>

    <div style="background: #fdfbf7; border: 1px solid #f1e9dc; border-radius: 10px; padding: 20px; margin-bottom: 24px; text-align: center;">
        <p style="color: #4b5563; font-size: 14px; margin: 0 0 16px;">
            Click the secure button below to choose a new password for your KTM-WDC portal account:
        </p>
        <a href="{{ $targetUrl }}" style="display: inline-block; background: #d96b43; color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 700; font-size: 15px;">Reset My Password →</a>
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
        <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
            ⏱️ <strong>Security Notice:</strong> This reset link will automatically expire in <strong>{{ $expiry }} minutes</strong>. KTM-WDC personnel will never ask you for your password or verification codes.
        </p>
    </div>

    <div style="border-top: 1px solid #e5e7eb; padding-top: 14px; color: #9ca3af; font-size: 12px;">
        <p style="margin: 0;">If you didn't request a password reset, you can safely ignore this email. Your account remains secure.</p>
    </div>
</div>
@endsection
