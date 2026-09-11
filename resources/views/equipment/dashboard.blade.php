@extends('layouts.app')

@section('title', 'Equipment Owner Dashboard')
@section('header', 'Equipment Owner Dashboard')

@section('content')
<!-- Namaste Greeting -->
<div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 mb-6 border-l-4 border-orange-500">
    <div class="flex items-center">
        <div class="text-4xl mr-4 animate-wave" style="animation: wave 0.5s ease-in-out;">
            🙏
        </div>
        <div>
            <p class="text-gray-800 text-lg">
                <span class="font-bold">Namaste Equipment Owner</span>,
                <span class="text-orange-600 font-bold ml-1">{{ Auth::user()->name }}</span>
            </p>
            <p class="text-sm text-gray-500 mt-1">Manage your equipment, track jobs, and monitor earnings.</p>
        </div>
    </div>
</div>

<style>
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        75% { transform: rotate(-10deg); }
    }
    .animate-wave { animation: wave 0.5s ease-in-out; }
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
    }
    .status-active { background: #d1fae5; color: #059669; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-completed { background: #dbeafe; color: #2563eb; }
</style>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">My Equipment</p>
                <p class="text-3xl font-bold text-gray-800">{{ $myEquipment ?? 0 }}</p>
                <p class="text-green-500 text-sm mt-2">Total registered</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-tools text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Active Jobs</p>
                <p class="text-3xl font-bold text-gray-800">{{ $activeJobs ?? 0 }}</p>
                <p class="text-orange-500 text-sm mt-2">In progress</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-crane text-orange-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Completed Jobs</p>
                <p class="text-3xl font-bold text-gray-800">{{ $completedJobs ?? 0 }}</p>
                <p class="text-green-500 text-sm mt-2">Finished</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-md card-hover">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Earnings</p>
                <p class="text-3xl font-bold text-gray-800">रु {{ number_format($totalEarnings ?? 0) }}</p>
                <p class="text-green-500 text-sm mt-2">Lifetime</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-rupee-sign text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl p-6 shadow-md mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('equipment.register') }}" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition">
            <i class="fas fa-plus-circle text-orange-500 text-2xl mb-2"></i>
            <p class="text-sm font-semibold">Register Equipment</p>
        </a>
        <a href="{{ route('equipment.jobs.index') }}" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition">
            <i class="fas fa-briefcase text-orange-500 text-2xl mb-2"></i>
            <p class="text-sm font-semibold">View Jobs</p>
        </a>
        <a href="{{ route('equipment.list') }}" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition">
            <i class="fas fa-tools text-orange-500 text-2xl mb-2"></i>
            <p class="text-sm font-semibold">My Equipment</p>
        </a>
        <a href="#" class="bg-gray-50 p-4 rounded-lg text-center hover:bg-orange-50 transition">
            <i class="fas fa-chart-line text-orange-500 text-2xl mb-2"></i>
            <p class="text-sm font-semibold">Earnings Report</p>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- My Equipment List -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">My Equipment</h3>
            <a href="{{ route('equipment.list') }}" class="text-orange-500 hover:text-orange-600 text-sm">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($equipmentList ?? [] as $item)
            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold">{{ $item->name }}</p>
                        <p class="text-sm text-gray-600">Type: {{ $item->type }}</p>
                        <p class="text-xs text-gray-500">Model: {{ $item->model ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="status-badge status-{{ $item->status ?? 'available' }}">
                            {{ ucfirst($item->status ?? 'Available') }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-tools text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500">No equipment registered yet</p>
                <a href="{{ route('equipment.register') }}" class="text-orange-500 text-sm mt-2 inline-block">Register your first equipment →</a>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Recent Job Requests -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Job Requests</h3>
            <a href="{{ route('equipment.jobs.index') }}" class="text-orange-500 hover:text-orange-600 text-sm">View all</a>
        </div>
        <div class="space-y-3">
            @forelse($jobRequests ?? [] as $job)
            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">Job #{{ $job->id }}</p>
                        <p class="text-sm text-gray-600">Equipment: {{ $job->equipment->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">Client: {{ $job->client->name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="status-badge status-{{ $job->status ?? 'pending' }}">
                            {{ ucfirst($job->status ?? 'Pending') }}
                        </span>
                        @if(($job->status ?? '') == 'pending')
                        <form action="{{ route('equipment.jobs.accept', $job->id) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                Accept Job
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500">No job requests at the moment</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Completed Jobs -->
<div class="mt-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recently Completed Jobs</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Earnings</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentCompletedJobs ?? [] as $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">#{{ $job->id }}</td>
                        <td class="px-4 py-3">{{ $job->equipment->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $job->client->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-green-600 font-semibold">रु {{ number_format($job->price ?? 0) }}</td>
                        <td class="px-4 py-3">{{ isset($job->updated_at) ? $job->updated_at->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No completed jobs yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
