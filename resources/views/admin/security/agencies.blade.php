@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Security Agencies</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Agency Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agencies as $agency)
            <tr>
                <td>{{ $agency->id }}</td>
                <td>{{ $agency->agency_name }}</td>
                <td>{{ $agency->email }}</td>
                <td>
                    <span class="badge bg-{{ $agency->status === 'approved' ? 'success' : ($agency->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($agency->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.security.agency.show', $agency) }}" class="btn btn-sm btn-info">View</a>
                    @if($agency->status === 'pending')
                    <form action="{{ route('admin.security.agency.approve', $agency) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form action="{{ route('admin.security.agency.reject', $agency) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="text" name="reason" placeholder="Reason" required>
                        <button class="btn btn-sm btn-danger">Reject</button>
                    </form>
                    @endif
                    <a href="{{ route('admin.security.agency.export', $agency) }}" class="btn btn-sm btn-secondary">PDF</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $agencies->links() }}
</div>
@endsection