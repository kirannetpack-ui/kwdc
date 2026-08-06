@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'Profile Settings')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border rounded-lg">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border rounded-lg">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Role</label>
                <input type="text" value="{{ ucfirst($user->role ?? 'User') }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100">
            </div>
            
            <div class="col-span-2">
                <hr class="my-4">
                <h3 class="text-lg font-bold mb-4">Change Password (Optional)</h3>
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg">
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 mt-6">
            <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection