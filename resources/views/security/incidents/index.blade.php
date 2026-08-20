@extends('layouts.security')

@section('title', 'Security Incidents')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Security Incidents</h2>
            <p class="text-muted mb-0">{{ $agency->agency_name }}</p>
        </div>
        <a href="{{ route('security.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Warehouse</th>
                        <th>Category</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Reported By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                        <tr>
                            <td>{{ optional($incident->incident_time)->format('d M Y, H:i') ?? 'N/A' }}</td>
                            <td>{{ $incident->warehouse?->name ?? 'N/A' }}</td>
                            <td>{{ $incident->category }}</td>
                            <td>{{ ucfirst($incident->severity) }}</td>
                            <td>{{ ucfirst($incident->status) }}</td>
                            <td>{{ $incident->reportedBy?->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No incidents reported.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $incidents->links() }}
    </div>
</div>
@endsection
