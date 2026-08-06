@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Simple Warehouse Registration</h1>
    
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    
    <form method="POST" action="{{ route('warehouses.store') }}">
        @csrf
        <input type="hidden" name="length_ft" value="10">
        <input type="hidden" name="width_ft" value="10">
        <input type="hidden" name="height_ft" value="10">
        
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Location:</label>
            <input type="text" name="location" required>
        </div>
        <div>
            <label>Warehouse Type:</label>
            <select name="warehouse_type" required>
                <option value="building">Building</option>
                <option value="plot_land">Plot Land</option>
                <option value="cold_storage">Cold Storage</option>
            </select>
        </div>
        <div>
            <label>Price per sq ft:</label>
            <input type="number" name="price" required>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
@endsection