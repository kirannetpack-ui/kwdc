<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserContact;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $contacts = UserContact::where('user_id', $user->id)->get();
        return view('profile.edit', compact('user', 'contacts'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $user->update($request->all());

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password updated successfully!');
    }

    public function contacts()
    {
        $user = Auth::user();
        $contacts = UserContact::where('user_id', $user->id)->get();
        return view('profile.contacts', compact('contacts'));
    }

    public function addContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'relationship' => 'nullable|string|max:50',
        ]);

        UserContact::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'relationship' => $request->relationship,
        ]);

        return redirect()->route('profile.contacts')
            ->with('success', 'Contact added successfully!');
    }

    public function destroyContact($id)
    {
        $contact = UserContact::where('user_id', Auth::id())->findOrFail($id);
        $contact->delete();

        return redirect()->route('profile.contacts')
            ->with('success', 'Contact deleted successfully!');
    }
}