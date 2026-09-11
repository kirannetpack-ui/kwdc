@extends('layouts.app')

@section('title', 'Warehouse Requests')
@section('header', 'Warehouse Requests')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-clipboard-list me-2"></i>
        Warehouse Requests
        <span class="badge bg-primary ms-2">{{ $warehouseRequests->total() }}</span>
    </div>
    <div class="card-body">
        @if($warehouseRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Warehouse</th>
                            <th>Request Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouseRequests as $request)
                        <tr>
                            <td><a href="{{ route('warehouse-requests.show', $request->id) }}">#{{ $request->id }}</a></td>
                            <td>
                                <strong>{{ $request->client->name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $request->client->email ?? '' }}</small>
                            </td>
                            <td>{{ $request->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($request->start_date && $request->end_date)
                                    {{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} - 
                                    {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ $request->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($request->status == 'pending')
                                    <form action="{{ route('property.requests.approve', $request->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('property.requests.reject', $request->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">No actions</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $warehouseRequests->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list text-muted fa-4x mb-3"></i>
                <h5 class="text-muted">No warehouse requests</h5>
                <p class="text-muted">Clients will request your warehouses here</p>
            </div>
        @endif
    </div>
</div>
@endsection
