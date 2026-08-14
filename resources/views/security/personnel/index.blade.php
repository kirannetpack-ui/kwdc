@extends('layouts.security')

@section('title', 'Personnel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Security Personnel</h2>
    <a href="{{ route('security.personnel.create') }}" class="btn btn-primary">+ Add Personnel</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Position</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($personnel as $p)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->position }}</td>
            <td>{{ $p->phone }}</td>
            <td><span class="badge bg-{{ $p->status == 'active' ? 'success' : ($p->status == 'inactive' ? 'danger' : 'warning') }}">{{ $p->status }}</span></td>
            <td>
                <a href="{{ route('security.personnel.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('security.personnel.destroy', $p) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No personnel found.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $personnel->links() }}
@endsection