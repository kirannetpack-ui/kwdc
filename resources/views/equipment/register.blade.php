@extends('layouts.app')

@section('title', 'Register Equipment')
@section('header', 'Register Your Equipment')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('equipment.store') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📋 Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Equipment Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Equipment Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:border-orange-500">
                        <option value="">Select Type</option>
                        <option value="jcb">JCB / Excavator</option>
                        <option value="crane">Crane</option>
                        <option value="dozer">Dozer / Bulldozer</option>
                        <option value="loader">Loader</option>
                        <option value="backhoe">Backhoe Loader</option>
                        <option value="forklift">Forklift</option>
                        <option value="compactor">Compactor / Roller</option>
                        <option value="concrete_mixer">Concrete Mixer</option>
                        <option value="other">Other</option>
                    </select>
                    @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Model Number</label>
                    <input type="text" name="model" class="w-full px-4 py-2 border rounded-lg">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Manufacturing Year</label>
                    <input type="number" name="year" class="w-full px-4 py-2 border rounded-lg" placeholder="e.g., 2020">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Describe your equipment..."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Specifications -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">⚙️ Specifications</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Operating Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Engine Power (HP)</label>
                    <input type="number" step="0.01" name="engine_power" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Bucket Capacity (m³)</label>
                    <input type="number" step="0.01" name="bucket_capacity" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Max Reach (m)</label>
                    <input type="number" step="0.01" name="max_reach" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>
        
        <!-- Pricing -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">💰 Pricing</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Daily Rate (NPR)</label>
                    <input type="number" step="0.01" name="daily_rate" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Weekly Rate (NPR)</label>
                    <input type="number" step="0.01" name="weekly_rate" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Monthly Rate (NPR)</label>
                    <input type="number" step="0.01" name="monthly_rate" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Security Deposit (NPR)</label>
                    <input type="number" step="0.01" name="security_deposit" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
        </div>
        
        <!-- Availability -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📅 Availability</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Location *</label>
                    <input type="text" name="location" required class="w-full px-4 py-2 border rounded-lg" placeholder="Where is the equipment located?">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg">
                        <option value="available">Available</option>
                        <option value="rented">Currently Rented</option>
                        <option value="maintenance">Under Maintenance</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Equipment Photos -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📸 Equipment Photos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Front View Photo</label>
                    <input type="file" name="front_photo" accept="image/*" class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Side View Photo</label>
                    <input type="file" name="side_photo" accept="image/*" class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Working Photo</label>
                    <input type="file" name="working_photo" accept="image/*" class="w-full">
                </div>
            </div>
        </div>
        
        <!-- Documents -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📄 Documents</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Registration Document</label>
                    <input type="file" name="registration_doc" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Insurance Document</label>
                    <input type="file" name="insurance_doc" accept=".pdf,.jpg,.jpeg,.png" class="w-full">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 mt-4">
            <a href="{{ route('equipment.list') }}" class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-save mr-2"></i> Register Equipment
            </button>
        </div>
    </form>
</div>
@endsection