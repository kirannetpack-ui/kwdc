<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:client,driver,property_owner,equipment_owner'],
        ]);
    }

    protected function create(array $data)
    {
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

        // Send admin notification
        $this->adminEmailService->notifyNewUser($user);

        // Send welcome email to user (optional)
        $this->sendWelcomeEmail($user);

        return $user;
    }

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
}