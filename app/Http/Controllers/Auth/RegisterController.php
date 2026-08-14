<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use App\Models\PropertyOwner;
use App\Models\EquipmentOwner;
use App\Models\SecurityAgency;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\AdminEmailService;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    protected $adminEmailService;

    public function __construct(AdminEmailService $adminEmailService)
    {
        $this->middleware('guest');
        $this->adminEmailService = $adminEmailService;
    }

    /**
     * Get a validator for an incoming registration request.
     * Adds conditional validation for role-specific fields.
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:client,driver,property_owner,equipment_owner,security_agency'],
        ];

        // Conditional validation based on role
        if ($data['role'] === 'driver') {
            $rules['vehicle_type'] = ['nullable', 'string', 'max:50'];
            $rules['license_number'] = ['nullable', 'string', 'max:50'];
        }

        if ($data['role'] === 'property_owner') {
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
        }

        if ($data['role'] === 'equipment_owner') {
            $rules['equipment_type'] = ['nullable', 'string', 'max:255'];
        }

        if ($data['role'] === 'security_agency') {
            $rules['agency_name'] = ['required', 'string', 'max:255'];
            $rules['registration_number'] = ['required', 'string', 'max:100'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     * Also creates the corresponding role-specific profile.
     */
    protected function create(array $data)
    {
        // 1. Create the user
        $user = User::create([
            'user_code' => $this->generateUserCode($data['role']),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 2. Create role-specific profile
        switch ($data['role']) {
            case 'driver':
                Driver::create([
                    'user_id' => $user->id,
                    'vehicle_type' => $data['vehicle_type'] ?? 'Standard',
                    'license_number' => $data['license_number'] ?? null,
                ]);
                break;

            case 'property_owner':
                PropertyOwner::create([
                    'user_id' => $user->id,
                    'company_name' => $data['company_name'] ?? null,
                ]);
                break;

            case 'equipment_owner':
                EquipmentOwner::create([
                    'user_id' => $user->id,
                    'equipment_type' => $data['equipment_type'] ?? null,
                ]);
                break;

            case 'security_agency':
                SecurityAgency::create([
                    'user_id' => $user->id,
                    'agency_name' => $data['agency_name'],
                    'registration_number' => $data['registration_number'],
                    'status' => 'pending',  // Admin will approve later
                ]);
                break;

            // Client does not need a separate profile
            case 'client':
            default:
                break;
        }

        // 3. Send admin notification (existing)
        $this->adminEmailService->notifyNewUser($user);

        // 4. Send welcome email (existing)
        $this->sendWelcomeEmail($user);

        return $user;
    }

    /**
     * Generate a unique user code (existing logic – unchanged)
     */
    private function generateUserCode($role)
    {
        $prefix = strtoupper(substr($role, 0, 3));
        $year = date('Y');
        $random = strtoupper(Str::random(6));
        
        $code = $prefix . $year . $random;
        
        while (User::where('user_code', $code)->exists()) {
            $random = strtoupper(Str::random(6));
            $code = $prefix . $year . $random;
        }
        
        return $code;
    }

    /**
     * Send welcome email (existing)
     */
    private function sendWelcomeEmail($user)
    {
        try {
            \Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Welcome to KTM-WDC');
            });
        } catch (\Exception $e) {
            \Log::error('Welcome email failed: ' . $e->getMessage());
        }
    }

    /**
     * The user has been registered – redirect to dashboard.
     */
    protected function registered(\Illuminate\Http\Request $request, $user)
    {
        return redirect()->route('dashboard');
    }
}