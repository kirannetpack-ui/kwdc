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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,property_owner,driver,equipment_owner'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role flag (ensure the field names match your migration)
        switch ($request->role) {
            case 'property_owner':
                $user->is_property_owner = true;
                break;
            case 'client':
                $user->is_client = true;
                break;
            case 'driver':
                $user->is_driver = true;
                break;
            case 'equipment_owner':
                $user->is_equipment_owner = true;
                break;
        }
        $user->save();

        event(new Registered($user));
        Auth::login($user);

        // Redirect based on role
        if ($user->is_driver) {
            return redirect()->route('driver.jobs');
        }
        if ($user->is_equipment_owner) {
            return redirect()->route('equipment.dashboard');
        }
        if ($user->is_property_owner) {
            return redirect()->route('dashboard');
        }
        return redirect()->intended(route('dashboard'));
    }
}