<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SecurityAgency;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\AdminEmailService;
use App\Services\ActivationCodeService;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/dashboard';

    protected $adminEmailService;
    protected $activationCodeService;

    public function __construct(AdminEmailService $adminEmailService, ActivationCodeService $activationCodeService)
    {
        $this->middleware('guest');
        $this->adminEmailService = $adminEmailService;
        $this->activationCodeService = $activationCodeService;
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
        $roleFlags = $this->roleFlags($data['role']);

        $user = User::create([
            'user_code' => $this->generateUserCode($data['role']),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'user_type' => $data['role'],
            'is_active' => true,
            'email_verified_at' => null,
        ] + $roleFlags);

        if ($data['role'] === 'security_agency') {
            SecurityAgency::create([
                'user_id' => $user->id,
                'agency_name' => $data['agency_name'],
                'registration_number' => $data['registration_number'],
                'status' => 'pending',
            ]);
        }

        $this->activationCodeService->send($user);
        $this->sendWelcomeEmail($user);
        $this->sendAdminRegistrationNotice($user);

        return $user;
    }

    private function roleFlags(string $role): array
    {
        return [
            'is_admin' => false,
            'is_client' => $role === 'client',
            'is_driver' => $role === 'driver',
            'is_property_owner' => $role === 'property_owner',
            'is_equipment_owner' => $role === 'equipment_owner',
        ];
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

    private function sendAdminRegistrationNotice($user): void
    {
        try {
            $this->adminEmailService->notifyNewUser($user);
        } catch (\Throwable $e) {
            Log::warning('Admin registration notice skipped: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * The user has been registered – redirect to dashboard.
     */
    protected function registered(\Illuminate\Http\Request $request, $user)
    {
        return redirect()
            ->route('activation.notice', ['email' => $user->email])
            ->with('status', 'We sent an activation code to your email.');
    }
}
