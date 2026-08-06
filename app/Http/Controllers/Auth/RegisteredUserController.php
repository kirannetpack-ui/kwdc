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
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Registration successful! Please wait for admin approval.');
    }
}