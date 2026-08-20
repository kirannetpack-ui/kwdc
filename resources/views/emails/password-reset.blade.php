@extends('emails.layout')

@section('content')
    <p style="margin: 0 0 10px; color: #f59e0b; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;">
        Account security
    </p>

    <h2 style="margin: 0; color: #111827; font-size: 26px; line-height: 1.25;">
        Reset your KTM-WDC password
    </h2>

    <p style="margin: 18px 0 0; color: #374151; font-size: 15px; line-height: 1.65;">
        Hello {{ $user->name }},
    </p>

    <p style="margin: 10px 0 0; color: #374151; font-size: 15px; line-height: 1.65;">
        We received a request to reset the password for your KTM-WDC account. Use the secure button below to choose a new password.
    </p>

    <p style="margin: 26px 0;">
        <a href="{{ $resetUrl }}" style="display: inline-block; background: #f59e0b; color: #ffffff; text-decoration: none; font-weight: 800; padding: 13px 18px; border-radius: 8px;">
            Reset password
        </a>
    </p>

    <div style="margin: 0 0 22px; padding: 18px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px;">
        <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.6;">
            This password reset link expires in {{ $expiresInMinutes }} minutes. KTM-WDC will never ask you to share your password, reset link, verification code, or payment details by email.
        </p>
    </div>

    <p style="margin: 0 0 16px; color: #374151; font-size: 15px; line-height: 1.65;">
        If the button does not work, copy and paste this secure link into your browser:
    </p>

    <p style="margin: 0 0 24px; word-break: break-all; color: #2563eb; font-size: 13px; line-height: 1.55;">
        {{ $resetUrl }}
    </p>

    <div style="border-top: 1px solid #e5e7eb; padding-top: 18px;">
        <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
            If you did not request a password reset, ignore this email. Your password will stay unchanged.
        </p>
    </div>
@endsection
