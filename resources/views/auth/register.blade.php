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
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
            max-height: 95vh;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .register-card::-webkit-scrollbar { width: 6px; }
        .register-card::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .register-card::-webkit-scrollbar-thumb { background: #f59e0b; border-radius: 10px; }
        
        .register-header { text-align: center; margin-bottom: 32px; }
        .register-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 36px;
            color: white;
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.3);
        }
        .register-header h1 { 
            font-size: 28px; 
            font-weight: 800; 
            color: #1f2937;
            letter-spacing: -0.5px;
        }
        .register-header p { 
            color: #6b7280; 
            font-size: 15px;
            margin-top: 4px;
        }
        
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
            letter-spacing: -0.2px;
        }
        .form-group label i { margin-right: 6px; color: #f59e0b; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9fafb;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.12);
            background: white;
        }
        .form-group textarea { resize: vertical; min-height: 60px; }
        .form-group select { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
        .helper-text { font-size: 12px; color: #6b7280; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
        .helper-text i { color: #9ca3af; }
        
        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
            margin-top: 4px;
        }
        .role-option {
            padding: 14px 10px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f9fafb;
            position: relative;
        }
        .role-option:hover { 
            border-color: #f59e0b; 
            background: #fffbeb; 
            transform: translateY(-2px);
        }
        .role-option.selected {
            border-color: #f59e0b;
            background: #fffbeb;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }
        .role-option i { font-size: 28px; display: block; margin-bottom: 6px; }
        .role-option .role-name { font-size: 13px; font-weight: 700; color: #374151; display: block; }
        .role-option .role-desc { font-size: 10px; color: #6b7280; display: block; margin-top: 2px; }
        .role-option input[type="radio"] { display: none; }
        .role-option .check-mark {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #f59e0b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .role-option.selected .check-mark { display: flex; }
        
        .agency-fields { 
            background: #f3f4f6; 
            padding: 16px; 
            border-radius: 12px; 
            margin-top: 12px;
            display: none;
            border-left: 4px solid #f59e0b;
        }
        .agency-fields.show { display: block; }
        .agency-fields .form-group { margin-bottom: 14px; }
        .agency-fields .form-group:last-child { margin-bottom: 0; }
        .agency-fields label { font-size: 13px; }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: 0.3px;
        }
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(245, 158, 11, 0.4);
        }
        .btn-register:active { transform: scale(0.97); }
        .btn-register i { font-size: 18px; }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            padding-top: 18px;
        }
        .login-link a { color: #f59e0b; font-weight: 600; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        
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
        .success-box {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-row { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 16px; 
        }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
        
        /* Improve scrollbar appearance */
        .register-card::-webkit-scrollbar-thumb:hover { background: #d97706; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <div class="logo"><i class="fas fa-user-plus"></i></div>
            <h1>Create Account</h1>
            <p>Join the KTM-WDC Logistics & Warehouse Network</p>
        </div>

        @if(session('success'))
        <div class="success-box">
            <i class="fas fa-check-circle fa-lg"></i> {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="error-box">
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following:</strong>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <!-- Basic Information -->
            <div class="form-row">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           placeholder="e.g. Rajesh Sharma" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           placeholder="your@email.com" required>
                </div>
            </div>

<div class="form-row">
    <div class="form-group">
        <label for="date_of_birth"><i class="fas fa-birthday-cake"></i> Date of Birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" 
               placeholder="Select your date of birth" required>
        <div class="helper-text"><i class="fas fa-info-circle"></i> We'll send you birthday wishes!</div>
    </div>
</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                           placeholder="e.g. 9800000000" required>
                    <div class="helper-text"><i class="fas fa-info-circle"></i> Used for order updates</div>
                </div>
                <div class="form-group">
                    <label for="address"><i class="fas fa-map-pin"></i> Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" 
                           placeholder="Your physical address (optional)">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" id="password" 
                           placeholder="Min 8 characters" required>
                    <div class="helper-text"><i class="fas fa-shield-alt"></i> Must be at least 8 characters</div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           placeholder="Re-enter password" required>
                </div>
            </div>
            
            <!-- Role Selection -->
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> I want to register as</label>
                <div class="role-grid" id="roleSelector">
                    <label class="role-option {{ old('role', 'client') == 'client' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="client" {{ old('role', 'client') == 'client' ? 'checked' : '' }}>
                        <span class="check-mark"><i class="fas fa-check"></i></span>
                        <i class="fas fa-user-circle" style="color: #3b82f6;"></i>
                        <span class="role-name">Client</span>
                        <span class="role-desc">Request warehouse & services</span>
                    </label>
                    <label class="role-option {{ old('role') == 'driver' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="driver" {{ old('role') == 'driver' ? 'checked' : '' }}>
                        <span class="check-mark"><i class="fas fa-check"></i></span>
                        <i class="fas fa-truck" style="color: #f59e0b;"></i>
                        <span class="role-name">Driver</span>
                        <span class="role-desc">Transport & delivery</span>
                    </label>
                    <label class="role-option {{ old('role') == 'property_owner' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="property_owner" {{ old('role') == 'property_owner' ? 'checked' : '' }}>
                        <span class="check-mark"><i class="fas fa-check"></i></span>
                        <i class="fas fa-warehouse" style="color: #8b5cf6;"></i>
                        <span class="role-name">Property Owner</span>
                        <span class="role-desc">List warehouse space</span>
                    </label>
                    <label class="role-option {{ old('role') == 'equipment_owner' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="equipment_owner" {{ old('role') == 'equipment_owner' ? 'checked' : '' }}>
                        <span class="check-mark"><i class="fas fa-check"></i></span>
                        <i class="fas fa-tools" style="color: #10b981;"></i>
                        <span class="role-name">Equipment Owner</span>
                        <span class="role-desc">Equipment rental</span>
                    </label>
                    <label class="role-option {{ old('role') == 'security_agency' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="security_agency" {{ old('role') == 'security_agency' ? 'checked' : '' }}>
                        <span class="check-mark"><i class="fas fa-check"></i></span>
                        <i class="fas fa-shield-alt" style="color: #ef4444;"></i>
                        <span class="role-name">Security Agency</span>
                        <span class="role-desc">Partner for security services</span>
                    </label>
                </div>
                <div class="helper-text" style="margin-top: 8px;">
                    <i class="fas fa-info-circle"></i> Choose the role that best fits your business
                </div>
            </div>

            <!-- Security Agency Fields (Hidden by default) -->
            <div id="agencyFields" class="agency-fields {{ old('role') == 'security_agency' ? 'show' : '' }}">
                <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 700; color: #1e3c72;">
                    <i class="fas fa-building"></i> Agency Details
                </h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="agency_name"><i class="fas fa-signature"></i> Agency Name</label>
                        <input type="text" name="agency_name" id="agency_name" value="{{ old('agency_name') }}" 
                               placeholder="e.g. Everest Security Solutions">
                    </div>
                    <div class="form-group">
                        <label for="registration_number"><i class="fas fa-id-card"></i> Registration Number</label>
                        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" 
                               placeholder="Company registration #">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="license_number"><i class="fas fa-certificate"></i> License Number</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" 
                               placeholder="Security license #">
                    </div>
                    <div class="form-group">
                        <label for="pan_vat_number"><i class="fas fa-file-invoice"></i> PAN / VAT Number</label>
                        <input type="text" name="pan_vat_number" id="pan_vat_number" value="{{ old('pan_vat_number') }}" 
                               placeholder="PAN or VAT #">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="year_established"><i class="fas fa-calendar-alt"></i> Year Established</label>
                        <input type="number" name="year_established" id="year_established" value="{{ old('year_established') }}" 
                               placeholder="e.g. 2015" min="1900" max="{{ date('Y') }}">
                    </div>
                    <div class="form-group">
                        <label for="services_offered"><i class="fas fa-concierge-bell"></i> Services Offered</label>
                        <input type="text" name="services_offered" id="services_offered" value="{{ old('services_offered') }}" 
                               placeholder="e.g. CCTV, Patrol, Access Control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Agency Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                               placeholder="Primary contact number">
                    </div>
                    <div class="form-group">
                        <label for="emergency_phone"><i class="fas fa-phone-alt"></i> Emergency Phone</label>
                        <input type="text" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone') }}" 
                               placeholder="24/7 emergency contact">
                    </div>
                </div>
                <div class="form-group">
                    <label for="certifications"><i class="fas fa-award"></i> Certifications (comma separated)</label>
                    <input type="text" name="certifications" id="certifications" value="{{ old('certifications') }}" 
                           placeholder="e.g. ISO 9001, NSI Gold">
                </div>
                <div class="form-group">
                    <label for="address"><i class="fas fa-map-marker-alt"></i> Agency Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" 
                           placeholder="Full address of agency">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label style="font-weight: normal; font-size: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="terms" {{ old('terms') ? 'checked' : '' }} required>
                    <span>I agree to the <a href="#" style="color: #f59e0b; text-decoration: none; font-weight: 600;">Terms of Service</a> and <a href="#" style="color: #f59e0b; text-decoration: none; font-weight: 600;">Privacy Policy</a></span>
                </label>
            </div>
            
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        // Role selection toggle
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
                // Show/hide agency fields
                toggleAgencyFields();
            });
        });

        function toggleAgencyFields() {
            const selectedRole = document.querySelector('input[name="role"]:checked');
            const agencyFields = document.getElementById('agencyFields');
            if (selectedRole && selectedRole.value === 'security_agency') {
                agencyFields.classList.add('show');
                // Make fields required (optional)
                document.querySelectorAll('#agencyFields input, #agencyFields textarea').forEach(input => {
                    if (input.name && input.name !== 'phone' && input.name !== 'address') {
                        input.required = true;
                    }
                });
            } else {
                agencyFields.classList.remove('show');
                document.querySelectorAll('#agencyFields input, #agencyFields textarea').forEach(input => {
                    input.required = false;
                });
            }
        }

        // On page load, check if security agency is selected
        document.addEventListener('DOMContentLoaded', function() {
            toggleAgencyFields();
        });
    </script>
</body>
</html>