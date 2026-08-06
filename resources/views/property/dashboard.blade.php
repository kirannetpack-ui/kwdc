@extends('layouts.app')

@section('title', 'Property Owner Dashboard')
@section('header', 'Property Owner Dashboard')

@section('content')
<!-- Namaste Greeting -->
<div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 mb-6 border-l-4 border-orange-500">
    <div class="flex items-center">
        <div class="text-3xl mr-3 animate-wave">🙏</div>
        <div>
            <p class="text-gray-800">
                <span class="font-bold">Namaste Property Owner</span>,
                <span class="text-orange-600 font-bold ml-1">{{ Auth::user()->name }}</span>
            </p>
            <p class="text-sm text-gray-500">Manage your warehouses and track earnings</p>
        </div>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <div>
            <strong>Success!</strong> {{ session('success') }}
            <p class="text-sm mt-1 text-green-600">Your warehouse has been submitted for admin approval. You will be notified once approved.</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">My Warehouses</p>
                <p class="text-3xl font-bold text-gray-800">{{ $myWarehouses ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-warehouse text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Approved</p>
                <p class="text-3xl font-bold text-gray-800">{{ $approvedWarehouses ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-3xl font-bold text-gray-800">{{ $pendingWarehouses ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Earnings</p>
                <p class="text-3xl font-bold text-gray-800">रु {{ number_format($totalEarnings ?? 0) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-rupee-sign text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Warehouses -->
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">My Warehouses</h3>
        <a href="{{ route('warehouses.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition text-sm">
            <i class="fas fa-plus mr-2"></i> Register New
        </a>
    </div>
    
    <div class="space-y-3">
        @forelse($recentWarehouses ?? [] as $warehouse)
        <div class="border rounded-lg p-3 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $warehouse->name }}</p>
                <p class="text-sm text-gray-600">{{ $warehouse->location ?? $warehouse->address }}</p>
                <p class="text-sm text-orange-600">रु {{ number_format($warehouse->price_per_unit ?? 0) }}/sq ft</p>
            </div>
            <div class="text-right">
                <span class="status-badge status-{{ $warehouse->status }}">
                    {{ ucfirst($warehouse->status ?? 'Pending') }}
                </span>
                <div class="mt-2">
                    <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="text-blue-500 hover:text-blue-700 text-sm mr-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Delete this warehouse?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8">
            <i class="fas fa-warehouse text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No warehouses registered yet</p>
            <a href="{{ route('warehouses.create') }}" class="text-orange-500 hover:text-orange-600 mt-2 inline-block">
                Click here to register your first warehouse
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Pending Submissions Section -->
<div class="bg-white rounded-xl shadow-md p-6 mt-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-hourglass-half text-orange-500 mr-2"></i> Pending Submissions
    </h3>
    
    @php
        $pendingSubmissions = App\Models\Warehouse::where('owner_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp
    
    @if($pendingSubmissions->count() > 0)
    <div class="space-y-3">
        @foreach($pendingSubmissions as $submission)
        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-3 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $submission->name }}</p>
                <p class="text-sm text-gray-600">{{ $submission->location ?? $submission->address }}</p>
                <p class="text-xs text-gray-500">Submitted: {{ $submission->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            <div class="text-right">
                <span class="status-badge status-pending flex items-center">
                    <i class="fas fa-clock mr-1"></i> Pending Approval
                </span>
                <p class="text-xs text-gray-500 mt-2">Waiting for admin review</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
        <p class="text-sm text-blue-700">
            <i class="fas fa-info-circle mr-1"></i> 
            Your warehouses are under review. Once approved by admin, they will appear in your active warehouses list.
        </p>
    </div>
    @else
    <div class="text-center py-6">
        <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
        <p class="text-gray-500">No pending submissions</p>
        <p class="text-sm text-gray-400">All your warehouses have been reviewed</p>
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <a href="{{ route('warehouses.create') }}" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition border">
        <i class="fas fa-plus-circle text-orange-500 text-2xl mb-2"></i>
        <p class="font-semibold">Register Warehouse</p>
        <p class="text-sm text-gray-500">Add new property</p>
    </a>
    <a href="{{ route('warehouses.index') }}" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition border">
        <i class="fas fa-warehouse text-orange-500 text-2xl mb-2"></i>
        <p class="font-semibold">View All Warehouses</p>
        <p class="text-sm text-gray-500">Manage your properties</p>
    </a>
    <a href="#" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition border">
        <i class="fas fa-chart-line text-orange-500 text-2xl mb-2"></i>
        <p class="font-semibold">Earnings Report</p>
        <p class="text-sm text-gray-500">View analytics</p>
    </a>
</div>

<style>
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        75% { transform: rotate(-10deg); }
    }
    .animate-wave {
        animation: wave 0.5s ease-in-out;
    }
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #059669; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
</style>
@endsection