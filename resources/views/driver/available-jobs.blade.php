@extends('layouts.app')

@section('title', 'Available Jobs')
@section('header', 'Available Jobs for You')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Jobs Near You</h3>
    
    <div class="space-y-4">
        @forelse($availableJobs ?? [] as $job)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold text-lg">Dispatch Order #{{ $job->id }}</p>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i> 
                        Pickup: {{ $job->pickup_address ?? 'N/A' }}
                    </p>
                    <p class="text-gray-600">
                        <i class="fas fa-flag-checkered text-orange-500 mr-1"></i> 
                        Delivery: {{ $job->delivery_address ?? 'N/A' }}
                    </p>
                    @if($job->amount)
                    <p class="text-green-600 font-semibold mt-2">
                        Earnings: रु {{ number_format($job->amount) }}
                    </p>
                    @endif
                </div>
                <form action="{{ route('driver.jobs.accept', $job->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-check mr-2"></i> Accept Job
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No available jobs at the moment</p>
            <p class="text-gray-400">Check back later for new opportunities</p>
        </div>
        @endforelse
    </div>
</div>
@endsection