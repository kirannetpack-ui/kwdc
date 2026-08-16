<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simple Login Test</title>
</head>
<body>
    <h1>🔐 Simple Login Test</h1>
    <p><strong>Session ID:</strong> {{ session()->getId() }}</p>
    <p><strong>CSRF Token:</strong> {{ csrf_token() }}</p>
    <p><strong>Session Domain:</strong> {{ config('session.domain') }}</p>
    
    <form method="POST" action="{{ route('login') }}" style="margin-top: 20px;">
        @csrf
        <div style="margin-bottom: 10px;">
            <label>Email:</label><br>
            <input type="email" name="email" value="" autocomplete="username" style="width: 300px; padding: 8px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>Password:</label><br>
            <input type="password" name="password" autocomplete="current-password" style="width: 300px; padding: 8px;">
        </div>
        <button type="submit" style="padding: 10px 30px; background: #f59e0b; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Login
        </button>
    </form>
    
    @if(session('error'))
        <div style="color: red; margin-top: 20px;">
            Error: {{ session('error') }}
        </div>
    @endif
</body>
</html>
