@extends('emails.layout')

@section('content')
    <h2>Activate your KTM-WDC account</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Use this activation code to verify your email address and finish setting up your account:</p>
    <p style="font-size: 28px; font-weight: 700; letter-spacing: 6px; text-align: center; margin: 24px 0;">
        {{ $activationCode }}
    </p>
    <p>This code expires in 30 minutes. If you did not create this account, you can ignore this email.</p>
@endsection
