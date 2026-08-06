@extends('layouts.app')

@section('title', 'Available Jobs')

@section('header', 'Available Jobs')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">
            <i class="fas fa-search text-orange-500 mr-2"></i> Available Delivery Jobs
        </h3>
        <span class="text-sm text-gray-500">{{ $jobs->total() ?? 0 }} jobs available</span>
    </div>

    @if(isset($jobs) && $jobs->count() > 0)
    <div class="space-y-4">
        @foreach($jobs as $job)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">#{{ $job->id }}</span>
                        <span class="status-badge status-pending">Available</span>
                    </div>
                    <p class="font-medium text-gray-800">
                        <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                        {{ $job->pickup_location ?? 'Pickup Location' }}
                        <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                        <i class="fas fa-flag-checkered text-green-500 mr-1"></i>
                        {{ $job->delivery_location ?? 'Delivery Location' }}
                    </p>
                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-500">
                        <span><i class="fas fa-box mr-1"></i> {{ $job->item_description ?? 'General Cargo' }}</span>
                        @if($job->quantity)
                        <span><i class="fas fa-cubes mr-1"></i> Qty: {{ $job->quantity }}</span>
                        @endif
                        @if($job->weight_kg)
                        <span><i class="fas fa-weight-hanging mr-1"></i> {{ $job->weight_kg }} kg</span>
                        @endif
                        @if($job->distance_km)
                        <span><i class="fas fa-road mr-1"></i> {{ number_format($job->distance_km, 1) }} km</span>
                        @endif
                        <span><i class="fas fa-calendar mr-1"></i> Posted: {{ $job->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="text-right">
                    @if($job->price)
                        <p class="text-xl font-bold text-green-600">रु {{ number_format($job->price, 2) }}</p>
                        <a href="{{ route('driver.jobs.accept', $job->id) }}" 
                           onclick="return confirm('Accept this job?')"
                           class="inline-block mt-2 bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">
                            <i class="fas fa-check mr-1"></i> Accept
                        </a>
                    @else
                        <button onclick="showProposalModal({{ $job->id }})" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">
                            <i class="fas fa-gavel mr-1"></i> Propose Price
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <i class="fas fa-tasks text-gray-400 text-5xl mb-3"></i>
        <p class="text-gray-500 text-lg">No available jobs at the moment</p>
        <p class="text-gray-400 text-sm mt-1">Check back later for new delivery requests</p>
    </div>
    @endif
</div>

<!-- Proposal Modal -->
<div id="proposalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-gavel text-orange-500 mr-2"></i> Propose Your Price
            </h3>
            <button onclick="closeProposalModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="proposalForm" method="POST">
            @csrf
            <div class="p-4">
                <label class="block text-gray-700 font-medium mb-2">Your Proposed Price (रु)</label>
                <input type="number" name="proposed_price" step="0.01" min="0" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-orange-500"
                    placeholder="Enter your price">
                <p class="text-xs text-gray-500 mt-2">The client will review your proposal and may accept or counter-offer</p>
            </div>
            <div class="flex justify-end p-4 border-t">
                <button type="button" onclick="closeProposalModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg mr-2">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Submit Proposal</button>
            </div>
        </form>
    </div>
</div>php artisan serve



<script>
    function showProposalModal(jobId) {
        const modal = document.getElementById('proposalModal');
        const form = document.getElementById('proposalForm');
        form.action = `/driver/propose-price/${jobId}`;
        modal.classList.remove('hidden');
    }
    
    function closeProposalModal() {
        document.getElementById('proposalModal').classList.add('hidden');
    }
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProposalModal();
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('proposalModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProposalModal();
        }
    });
</script>
@endsection