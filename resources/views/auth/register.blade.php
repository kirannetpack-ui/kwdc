<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM-WDC - Register</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f3f4f6; }
        
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            padding: 48px;
            width: 100%;
            max-width: 560px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-height: 95vh;
            overflow-y: auto;
        }
        
        .register-card::-webkit-scrollbar {
            width: 5px;
        }
        
        .register-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .register-card::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 10px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
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
        
        .register-header h1 {
            font-size: 26px;
            font-weight: 800;
            color: #1f2937;
        }
        
        .register-header p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .form-group label i {
            margin-right: 6px;
            color: #f59e0b;
        }
        
        .form-group .input-wrapper {
            position: relative;
        }
        
        .form-group .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 16px 11px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
            background: white;
        }
        
        .form-group select {
            appearance: none;
            padding-right: 40px;
            cursor: pointer;
        }
        
        .form-group .select-wrapper {
            position: relative;
        }
        
        .form-group .select-wrapper i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group .helper-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .form-group .helper-text i {
            margin-right: 4px;
        }
        
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
        
        .role-option:hover {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        
        .role-option.selected {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }
        
        .role-option i {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
        }
        
        .role-option .role-name {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        
        .role-option input[type="radio"] {
            display: none;
        }
        
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
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .login-link {
            text-align: center;
            margin-top: 18px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .login-link a {
            color: #f59e0b;
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .error-box {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #dc2626;
            font-size: 14px;
        }
        
        .error-box ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .success-box {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #065f46;
            font-size: 14px;
        }
        
        @media (max-width: 640px) {
            .register-card {
                padding: 24px;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .role-selector {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <div class="logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Create Account</h1>
                <p>Join KTM-WDC - Warehouse & Distribution Connect</p>
            </div>
            
            <!-- Success Message -->
            @if(session('success'))
            <div class="success-box">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            @endif
            
            <!-- Error Display -->
            @if($errors->any())
            <div class="error-box">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Name -->
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                               placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <!-- Phone -->
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               placeholder="Enter your phone number (e.g., 9800000000)">
                    </div>
                    <div class="helper-text">
                        <i class="fas fa-info-circle"></i> Used for order updates and notifications
                    </div>
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" 
                               placeholder="Enter a strong password (min 8 characters)" required>
                    </div>
                    <div class="helper-text">
                        <i class="fas fa-shield-alt"></i> Minimum 8 characters with at least one number
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               placeholder="Re-enter your password" required>
                    </div>
                </div>
                
                <!-- Role Selection -->
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> I want to register as</label>
                    <div class="role-selector" id="roleSelector">
                        <label class="role-option {{ old('role', 'client') == 'client' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="client" {{ old('role', 'client') == 'client' ? 'checked' : '' }}>
                            <i class="fas fa-user-circle" style="color: #3b82f6;"></i>
                            <span class="role-name">Client</span>
                            <span style="font-size: 10px; color: #6b7280; display: block;">Request warehouse & services</span>
                        </label>
                        <label class="role-option {{ old('role') == 'driver' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="driver" {{ old('role') == 'driver' ? 'checked' : '' }}>
                            <i class="fas fa-truck" style="color: #f59e0b;"></i>
                            <span class="role-name">Driver</span>
                            <span style="font-size: 10px; color: #6b7280; display: block;">Transport & delivery services</span>
                        </label>
                        <label class="role-option {{ old('role') == 'property_owner' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="property_owner" {{ old('role') == 'property_owner' ? 'checked' : '' }}>
                            <i class="fas fa-warehouse" style="color: #8b5cf6;"></i>
                            <span class="role-name">Property Owner</span>
                            <span style="font-size: 10px; color: #6b7280; display: block;">List your warehouse space</span>
                        </label>
                        <label class="role-option {{ old('role') == 'equipment_owner' ? 'selected' : '' }}">
                            <input type="radio" name="role" value="equipment_owner" {{ old('role') == 'equipment_owner' ? 'checked' : '' }}>
                            <i class="fas fa-tools" style="color: #10b981;"></i>
                            <span class="role-name">Equipment Owner</span>
                            <span style="font-size: 10px; color: #6b7280; display: block;">Equipment rental services</span>
                        </label>
                    </div>
                </div>
                
                <!-- Terms -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-weight: normal; font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="terms" {{ old('terms') ? 'checked' : '' }} required>
                        <span>
                            I agree to the 
                            <a href="#" style="color: #f59e0b; text-decoration: none;">Terms of Service</a> 
                            and 
                            <a href="#" style="color: #f59e0b; text-decoration: none;">Privacy Policy</a>
                        </span>
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
    </div>
    
    <script>
        // Role selector styling
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
        
        // Auto-select if already checked
        document.querySelectorAll('.role-option input[type="radio"]:checked').forEach(checked => {
            checked.closest('.role-option').classList.add('selected');
        });
    </script>
</body>
</html>