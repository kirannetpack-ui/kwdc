@extends('layouts.app')

@section('title', 'Forbidden')
@section('header', '403 - Forbidden')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-lock text-danger" style="font-size: 64px;"></i>
                    </div>
                    <h1 class="display-1 fw-bold text-danger">403</h1>
                    <h2 class="mb-4">Access Denied</h2>
                    <p class="text-muted mb-4">
                        You do not have permission to access this page. Please contact your administrator 
                        if you believe this is an error.
                    </p>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-orange">
                            <i class="fas fa-home me-2"></i>Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-orange {
        background: #f59e0b;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-orange:hover {
        background: #d97706;
        color: white;
    }
</style>
@endsection