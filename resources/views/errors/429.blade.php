@extends('layouts.app')

@section('title', 'Too Many Requests')
@section('header', '429 - Too Many Requests')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-tachometer-alt text-warning" style="font-size: 64px;"></i>
                    </div>
                    <h1 class="display-1 fw-bold text-warning">429</h1>
                    <h2 class="mb-4">Too Many Requests</h2>
                    <p class="text-muted mb-4">
                        You have made too many requests in a short period. Please wait a moment before trying again.
                    </p>
                    <div>
                        <a href="{{ url()->current() }}" class="btn btn-orange">
                            <i class="fas fa-sync me-2"></i>Try Again
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
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