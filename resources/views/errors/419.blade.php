@extends('layouts.app')

@section('title', 'Session Expired')
@section('header', '419 - Session Expired')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clock text-warning" style="font-size: 64px;"></i>
                    </div>
                    <h1 class="display-1 fw-bold text-warning">419</h1>
                    <h2 class="mb-4">Page Expired</h2>
                    <p class="text-muted mb-4">
                        Your session has expired. Please refresh the page and try again.
                    </p>
                    <div>
                        <a href="{{ url()->current() }}" class="btn btn-orange">
                            <i class="fas fa-sync me-2"></i>Refresh Page
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Again
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