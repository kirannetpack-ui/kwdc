@extends('layouts.app')

@section('title', 'Equipment Job Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Equipment Job Requests</h2>
<a href="{{ route('equipment.jobs.index') }}" class="btn btn-sm btn-primary">View All</a>
            <i class="fas fa-arrow-left me-2"></i>Back to Jobs
        </a>
    </div>

    @if(isset($requests) && $requests->count() > 0)
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
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->client->name ?? 'N/A' }}</td>
                                <td>{{ $request->equipment->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-warning">Pending</span>
                                </td>
                                <td>रु {{ number_format($request->price ?? 0, 2) }}</td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('equipment.jobs.accept', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Accept</button>
                                        </form>
                                        <form action="{{ route('equipment.jobs.reject', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this request?')">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($requests, 'links'))
                <div class="card-footer">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list text-muted" style="font-size: 48px;"></i>
                <h4 class="mt-3">No Job Requests</h4>
                <p class="text-muted">You don't have any pending equipment job requests.</p>
            </div>
        </div>
    @endif
</div>
@endsection