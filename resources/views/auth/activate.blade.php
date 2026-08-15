<x-guest-layout>
<div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Activate your account</h1>
    <p class="text-gray-600 mb-6">Enter the 6-digit code sent to your email address.</p>

    @if(session('status'))
        <div class="mb-4 p-3 rounded bg-blue-50 text-blue-700">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('activation.verify') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="mt-1 block w-full rounded border-gray-300">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="activation_code" class="block text-sm font-medium text-gray-700">Activation code</label>
            <input id="activation_code" name="activation_code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required class="mt-1 block w-full rounded border-gray-300 tracking-widest">
            @error('activation_code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-orange-500 text-white rounded px-4 py-2 font-semibold">Activate account</button>
    </form>

    <form method="POST" action="{{ route('activation.resend') }}" class="mt-4">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email) }}">
        <button type="submit" class="text-sm text-orange-600 font-semibold">Send a new code</button>
    </form>
</div>
</x-guest-layout>
