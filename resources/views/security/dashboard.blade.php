@extends('layouts.security')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h2>Welcome back, {{ Auth::user()->name }}</h2>
    <p>Here's what's happening with your logistics today.</p>

    @if(!$agencyExists)
        <div class="alert alert-warning">You do not have a security agency profile yet. Please contact admin.</div>
    @else
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Personnel</h5>
                        <p class="card-text display-4">{{ $personnelCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Goods</h5>
                        <p class="card-text display-4">{{ $goodsCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Active Assignments</h5>
                        <p class="card-text display-4">{{ $activeAssignments }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Incidents</h5>
                        <p class="card-text display-4">{{ $incidentsCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Agency Details</div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $agency->agency_name }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($agency->status) }}</p>
                        <p><strong>Registered:</strong> {{ $agency->created_at->format('d M Y') }}</p>
                        <p><strong>Agency Exists:</strong> Yes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Quick Actions</div>
                    <div class="card-body">
                        <a href="{{ route('security.personnel.create') }}" class="btn btn-primary d-block mb-2">Add Personnel</a>
                        <a href="{{ route('security.goods.index') }}" class="btn btn-secondary d-block mb-2">My Goods</a>
                        <a href="{{ route('security.assignments.index') }}" class="btn btn-success d-block">Assignments</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection