@extends('layouts.app')

@section('title', 'Active Equipment Jobs')
@section('header', 'Active Jobs')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-bold">Active Jobs</h2>
            <p class="text-sm text-gray-500 mt-1">Jobs currently in progress</p>
        </div>
        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
            <i class="fas fa-play-circle mr-1"></i> {{ $activeJobs->total() }} Active Jobs
        </span>
    </div>
    
    @if($activeJobs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($activeJobs as $job)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium">{{ $job->equipment->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $job->equipment->type ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $job->client->name ?? 'N/A' }}
                            <div class="text-xs text-gray-500">{{ $job->client->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div>{{ \Carbon\Carbon::parse($job->start_date)->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">to</div>
                            <div>{{ \Carbon\Carbon::parse($job->end_date)->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) }} days
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs 
                                @if($job->status == 'accepted') bg-blue-100 text-blue-600
                                @elseif($job->status == 'in_progress') bg-purple-100 text-purple-600
                                @elseif($job->status == 'assigned') bg-indigo-100 text-indigo-600
                                @else bg-gray-100 text-gray-600 @endif">
                                <i class="fas 
                                    @if($job->status == 'accepted') fa-check-circle
                                    @elseif($job->status == 'in_progress') fa-spinner fa-pulse
                                    @else fa-clock @endif mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                            @if($job->status == 'in_progress' && $job->started_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    Started: {{ \Carbon\Carbon::parse($job->started_at)->diffForHumans() }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ \Illuminate\Support\Str::limit($job->location, 30) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('equipment.jobs.show', $job->id) }}" 
                                   class="text-blue-500 hover:text-blue-700 transition"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($job->status == 'accepted')
                                <form action="{{ route('equipment.jobs.update-status', $job->id) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" 
                                            class="text-purple-500 hover:text-purple-700 transition"
                                            title="Mark In Progress"
                                            onclick="return confirm('Mark this job as in progress?')">
                                        <i class="fas fa-play-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if($job->status == 'in_progress')
                                <form action="{{ route('equipment.jobs.update-status', $job->id) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" 
                                            class="text-green-500 hover:text-green-700 transition"
                                            title="Mark Completed"
                                            onclick="return confirm('Mark this job as completed?')">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $activeJobs->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-play-circle text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No active jobs</p>
            <p class="text-gray-400 text-sm mt-2">When you accept job requests, they will appear here</p>
            <a href="{{ route('equipment.jobs.requests') }}" class="inline-block mt-4 text-orange-500 hover:text-orange-600">
                <i class="fas fa-clipboard-list mr-1"></i> View Pending Requests
            </a>
        </div>
    @endif
</div>
@endsection