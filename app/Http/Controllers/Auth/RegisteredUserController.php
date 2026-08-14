<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\UserWelcomeMail;
use App\Mail\AdminNewUserNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\SecurityAgency;
use App\Mail\AgencyWelcomeMail;
use App\Mail\AdminAgencyNotification;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,driver,equipment_owner,property_owner'],
'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'],
        ]);

        // Generate user code
        $year = date('Y');
        $prefix = match($request->role) {
            'client' => 'CLT',
            'driver' => 'DRV',
            'equipment_owner' => 'EQO',
            'property_owner' => 'PRP',
            default => 'USR',
        };
        
        $lastUser = User::where('user_code', 'like', "{$prefix}-{$year}-%")
            ->orderBy('user_code', 'desc')
            ->first();
        
        if ($lastUser && $lastUser->user_code) {
            $lastNum = intval(substr($lastUser->user_code, -4));
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }
        
        $userCode = "{$prefix}-{$year}-{$newNum}";

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'user_code' => $userCode,
            'is_client' => $request->role === 'client',
            'is_driver' => $request->role === 'driver',
            'is_equipment_owner' => $request->role === 'equipment_owner',
            'is_property_owner' => $request->role === 'property_owner',
            'status' => 'pending',
'date_of_birth' => $request->date_of_birth,
        ]);

// After user is created, check if role is 'security_agency'
if ($request->role === 'security_agency') {
    $agency = SecurityAgency::create([
        'user_id' => $user->id,
        'agency_name' => $request->agency_name,
        'registration_number' => $request->registration_number,
        'license_number' => $request->license_number,
        'address' => $request->address,
        'phone' => $request->phone,
        'emergency_phone' => $request->emergency_phone,
        'email' => $request->email,
        'services_offered' => $request->services_offered,
        'year_established' => $request->year_established,
        'pan_vat_number' => $request->pan_vat_number,
        'certifications' => $request->certifications,
        'status' => 'pending',
    ]);

    // Send welcome email to agency
    Mail::to($user->email)->send(new AgencyWelcomeMail($agency));
    
    // Send notification to admin
    $adminEmail = config('app.admin_email', 'kiran.kwdc@gmail.com');
    Mail::to($adminEmail)->send(new AdminAgencyNotification($agency));

    return redirect()->route('login')->with('status', 
        'Registration successful! Our team will review your agency profile and contact you within 24 hours.'
    );
}

// 1. Send welcome email to the user
Mail::to($user->email)->send(new UserWelcomeMail($user));

// 2. Send notification to admin
$adminEmail = config('app.admin_email', 'admin@ktmwdc.com');
Mail::to($adminEmail)->send(new AdminNewUserNotification($user));

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Registration successful! Please wait for admin approval.');
    }
}