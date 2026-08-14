<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM-WDC - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 48px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-header { text-align: center; margin-bottom: 32px; }
        .login-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 40px;
            color: white;
        }
        .login-header h1 { font-size: 28px; font-weight: 800; color: #1f2937; }
        .login-header p { color: #6b7280; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
            background: white;
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .form-options label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            cursor: pointer;
        }
        .form-options label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #f59e0b;
        }
        .form-options a { color: #f59e0b; text-decoration: none; font-weight: 600; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .register-link a { color: #f59e0b; font-weight: 600; text-decoration: none; }
        .error-box {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #dc2626;
            font-size: 14px;
        }
        .error-box ul { margin: 0; padding-left: 20px; }
        .debug-info {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 10px;
            margin-top: 15px;
            font-size: 12px;
            color: #6b7280;
            display: none; /* Hide by default, show for debugging */
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo"><i class="fas fa-warehouse"></i></div>
            <h1>KTM-WDC</h1>
            <p>Warehouse & Distribution Connect</p>
        </div>

        @if($errors->any())
        <div class="error-box">
            <strong>⚠️ Please fix:</strong>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- IMPORTANT: The form must be EXACTLY like this -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', 'admin@ktmwdc.com') }}" 
                       placeholder="Enter your email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" name="password" id="password" value="password123"
                       placeholder="Enter your password" required>
            </div>
            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
<a href="{{ route('password.request') }}">Forgot password?</a>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>

        <!-- Debug Information (remove after fixing) -->
        <div class="debug-info">
            <p><strong>Debug Info:</strong></p>
            <p>Session ID: {{ session()->getId() }}</p>
            <p>CSRF Token: {{ csrf_token() }}</p>
            <p>Session Domain: {{ config('session.domain') }}</p>
        </div>
    </div>
</body>
</html>