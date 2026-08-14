@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Birthday Banner -->
@if($isBirthday ?? false)
<div class="mb-6 p-6 rounded-lg text-center font-bold text-xl" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e;">
    <i class="fas fa-birthday-cake mr-3" style="font-size: 30px;"></i>
    {{ $birthdayMessage ?? 'Happy Birthday!' }}
</div>
@endif

<!-- Welcome Section -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <p class="text-gray-600 text-sm mb-1">Namaste, {{ $user->name ?? 'User' }} <span class="text-orange-600 text-sm">({{ ucfirst($role ?? 'user') }})</span></p>
        <h2 class="text-3xl font-bold text-gray-800">Welcome back, {{ $user->name ?? 'User' }}</h2>
        <p class="text-gray-500 mt-1">Here's what's happening with your logistics today.</p>
    </div>
    <div class="text-right">
        <p class="text-gray-600 text-sm">System Online</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('F j, Y') }}</p>
    </div>
</div>

<!-- Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @if(!empty($stats) && is_array($stats))
        @foreach($stats as $key => $value)
            @if(is_numeric($value) || is_bool($value))
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">
                            @if(is_bool($value))
                                {{ $value ? 'Yes' : 'No' }}
                            @else
                                {{ number_format($value) }}
                            @endif
                        </p>
                    </div>
                    <div class="p-3 rounded-lg" style="background-color: #eff6ff;">
                        <i class="fas fa-chart-bar text-2xl" style="color: #3b82f6;"></i>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @else
        <p class="text-gray-500">No statistics available.</p>
    @endif
</div>

<!-- Recent Activity Sections -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Recent Requests -->
    @if(!empty($recentRequests) && $recentRequests->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Requests</h3>
            <a href="{{ route('my-requests.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @foreach($recentRequests as $request)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Request #{{ $request->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #fed7aa; color: #92400e;">{{ ucfirst($request->status ?? 'pending') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Dispatches -->
    @if(!empty($recentDispatches) && $recentDispatches->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Dispatches</h3>
            <a href="{{ route('dispatch.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @foreach($recentDispatches as $dispatch)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Dispatch #{{ $dispatch->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">Driver: {{ $dispatch->driver->name ?? 'Unassigned' }}</p>
                    </div>
                    <a href="{{ route('tracking.shipment', $dispatch->id) }}" class="text-orange-500 text-sm hover:text-orange-600">Track →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Additional Sections -->
@if(!empty($recentPickups) && $recentPickups->count() > 0)
<div class="mt-6 bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Recent Pickups</h3>
        <a href="{{ route('pickup.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b-2 border-gray-200">
                <tr>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">ID</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Pickup Address</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Status</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPickups as $pickup)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4">#{{ $pickup->id }}</td>
                    <td class="py-3 px-4">{{ Str::limit($pickup->pickup_address ?? 'N/A', 30) }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #fed7aa; color: #92400e;">{{ ucfirst($pickup->status ?? 'pending') }}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-500">{{ $pickup->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
