@extends('layouts.security')

@section('title', 'Assignments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Security Assignments</h2>
    <a href="{{ route('security.assignments.create') }}" class="btn btn-primary">+ Create Assignment</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Warehouse</th>
            <th>Personnel</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Shift</th>
            <th>Status</th>
            <th>Total Cost</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($assignments as $assignment)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $assignment->warehouse->name ?? 'N/A' }}</td>
            <td>{{ $assignment->personnel->name ?? 'Unassigned' }}</td>
            <td>{{ $assignment->start_date }}</td>
            <td>{{ $assignment->end_date ?? '—' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $assignment->shift)) }}</td>
            <td>
                <span class="badge bg-{{ $assignment->status == 'active' ? 'success' : ($assignment->status == 'completed' ? 'info' : 'danger') }}">
                    {{ ucfirst($assignment->status) }}
                </span>
            </td>
            <td>${{ number_format($assignment->total_cost ?? 0, 2) }}</td>
            <td>
                <a href="{{ route('security.assignments.show', $assignment) }}" class="btn btn-sm btn-info">View</a>
                <a href="{{ route('security.assignments.edit', $assignment) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('security.assignments.destroy', $assignment) }}" method="POST" style="display:inline-block;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this assignment?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">No assignments yet. <a href="{{ route('security.assignments.create') }}">Create one</a>.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $assignments->links() }}
@endsection