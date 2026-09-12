@extends('emails.layout')

@section('content')
    <h2>{{ $reminder->title }}</h2>
    <p>Hello {{ $reminder->user->name }},</p>
    <p>This is your KTM-WDC reminder for {{ $reminder->starts_at->format('M d, Y h:i A') }}.</p>
    @if($reminder->notes)
        <p>{{ $reminder->notes }}</p>
    @endif
    <p>
        <a href="{{ route('reminders.index') }}" style="display:inline-block;background:#f59e0b;color:white;padding:10px 16px;border-radius:6px;text-decoration:none;">
            Open reminders
        </a>
    </p>
@endsection
