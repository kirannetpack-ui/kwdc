@extends('layouts.app')

@section('title', 'Equipment Jobs History')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Equipment Jobs History</h2>
<a href="{{ route('equipment.jobs.index') }}" class="btn btn-sm btn-primary">View All</a>
            <i class="fas fa-arrow-left me-2"></i>Back to Jobs
        </a>
    </div>

    @if(isset($history) && $history->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Equipment</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $job)
                            <tr>
                                <td>#{{ $job->id }}</td>
                                <td>{{ $job->client->name ?? 'N/A' }}</td>
                                <td>{{ $job->equipment->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $job->status === 'completed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td>रु {{ number_format($job->price ?? 0, 2) }}</td>
                                <td>{{ $job->completed_at ? $job->completed_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($history, 'links'))
                <div class="card-footer">
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-history text-muted" style="font-size: 48px;"></i>
                <h4 class="mt-3">No History</h4>
                <p class="text-muted">You don't have any completed equipment jobs yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection