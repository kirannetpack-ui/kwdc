@extends('layouts.security')

@section('title', 'My Goods')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Security Goods</h2>
    <a href="{{ route('security.goods.create') }}" class="btn btn-primary">+ Add Good</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($goods as $good)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $good->item_name }}</td>
            <td>{{ $good->category }}</td>
            <td>{{ $good->quantity_available }}</td>
            <td>${{ number_format($good->unit_price, 2) }}</td>
            <td><span class="badge bg-{{ $good->status == 'available' ? 'success' : ($good->status == 'rented' ? 'warning' : 'danger') }}">
                {{ ucfirst($good->status) }}
            </span></td>
            <td>
                <a href="{{ route('security.goods.show', $good) }}" class="btn btn-sm btn-info">View</a>
                <a href="{{ route('security.goods.edit', $good) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('security.goods.destroy', $good) }}" method="POST" style="display:inline-block;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No goods found. <a href="{{ route('security.goods.create') }}">Add your first item</a>.</td></tr>
        @endforelse
    </tbody>
</table>
{{ $goods->links() }}
@endsection