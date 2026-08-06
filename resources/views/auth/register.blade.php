<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM-WDC - Register</title>
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
        .register-card {
            background: white;
            border-radius: 20px;
            padding: 48px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-height: 95vh;
            overflow-y: auto;
        }
        .register-card::-webkit-scrollbar { width: 5px; }
        .register-card::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .register-card::-webkit-scrollbar-thumb { background: #f59e0b; border-radius: 10px; }
        
        .register-header { text-align: center; margin-bottom: 32px; }
        .register-header .logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 32px;
            color: white;
        }
        .register-header h1 { font-size: 26px; font-weight: 800; color: #1f2937; }
        .register-header p { color: #6b7280; font-size: 14px; }
        
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 11px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
            background: white;
        }
        .form-group select { appearance: none; cursor: pointer; }
        
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 4px;
        }
        .role-option {
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        .role-option:hover { border-color: #f59e0b; background: #fffbeb; }
        .role-option.selected {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }
        .role-option i { font-size: 24px; display: block; margin-bottom: 4px; }
        .role-option .role-name { font-size: 12px; font-weight: 600; color: #374151; }
        .role-option .role-desc { font-size: 10px; color: #6b7280; display: block; }
        .role-option input[type="radio"] { display: none; }
        
        .btn-register {
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
            margin-top: 8px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        .login-link {
            text-align: center;
            margin-top: 18px;
            color: #6b7280;
            font-size: 14px;
        }
        .login-link a { color: #f59e0b; font-weight: 600; text-decoration: none; }
        .error-box {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #dc2626;
            font-size: 14px;
        }
        .error-box ul { margin: 0; padding-left: 20px; }
        .helper-text { font-size: 12px; color: #6b7280; margin-top: 4px; }
        
        @media (max-width: 640px) {
            .register-card { padding: 24px; }
            .role-selector { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <div class="logo"><i class="fas fa-user-plus"></i></div>
            <h1>Create Account</h1>
            <p>Join KTM-WDC - Warehouse & Distribution Connect</p>
        </div>

        @if(session('success'))
        <div class="success-box" style="background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif

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

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                       placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       placeholder="Enter your email address" required>
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                       placeholder="Enter your phone number (e.g., 9800000000)">
                <div class="helper-text"><i class="fas fa-info-circle"></i> Used for order updates</div>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" 
                       placeholder="Enter a strong password (min 8 characters)" required>
                <div class="helper-text"><i class="fas fa-shield-alt"></i> Minimum 8 characters</div>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation"><i class="fas fa-check-circle"></i> Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       placeholder="Re-enter your password" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> I want to register as</label>
                <div class="role-selector" id="roleSelector">
                    <label class="role-option {{ old('role', 'client') == 'client' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="client" {{ old('role', 'client') == 'client' ? 'checked' : '' }}>
                        <i class="fas fa-user-circle" style="color: #3b82f6;"></i>
                        <span class="role-name">Client</span>
                        <span class="role-desc">Request warehouse & services</span>
                    </label>
                    <label class="role-option {{ old('role') == 'driver' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="driver" {{ old('role') == 'driver' ? 'checked' : '' }}>
                        <i class="fas fa-truck" style="color: #f59e0b;"></i>
                        <span class="role-name">Driver</span>
                        <span class="role-desc">Transport & delivery services</span>
                    </label>
                    <label class="role-option {{ old('role') == 'property_owner' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="property_owner" {{ old('role') == 'property_owner' ? 'checked' : '' }}>
                        <i class="fas fa-warehouse" style="color: #8b5cf6;"></i>
                        <span class="role-name">Property Owner</span>
                        <span class="role-desc">List your warehouse space</span>
                    </label>
                    <label class="role-option {{ old('role') == 'equipment_owner' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="equipment_owner" {{ old('role') == 'equipment_owner' ? 'checked' : '' }}>
                        <i class="fas fa-tools" style="color: #10b981;"></i>
                        <span class="role-name">Equipment Owner</span>
                        <span class="role-desc">Equipment rental services</span>
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: normal; font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="terms" {{ old('terms') ? 'checked' : '' }} required>
                    <span>I agree to the <a href="#" style="color: #f59e0b; text-decoration: none;">Terms of Service</a> and <a href="#" style="color: #f59e0b; text-decoration: none;">Privacy Policy</a></span>
                </label>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus mr-2"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html>