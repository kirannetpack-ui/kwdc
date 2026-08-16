@extends('layouts.app')

@section('title', 'Reminder Calendar')
@section('header', 'Reminder Calendar')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-semibold">
                <i class="fas fa-calendar-plus me-2"></i>Create Reminder
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reminders.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="starts_at">Date and time</label>
                        <input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required>
                        @error('starts_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="remind_at">Email reminder time</label>
                        <input class="form-control" id="remind_at" name="remind_at" type="datetime-local" value="{{ old('remind_at') }}">
                        @error('remind_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                        @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-warning w-100" type="submit">
                        <i class="fas fa-save me-1"></i>Save Reminder
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-calendar-alt me-2"></i>{{ $month->format('F Y') }}</span>
                <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-secondary" href="{{ route('reminders.index', ['month' => $month->copy()->subMonth()->toDateString()]) }}">Previous</a>
                    <a class="btn btn-outline-secondary" href="{{ route('reminders.index') }}">Today</a>
                    <a class="btn btn-outline-secondary" href="{{ route('reminders.index', ['month' => $month->copy()->addMonth()->toDateString()]) }}">Next</a>
                </div>
            </div>
            <div class="card-body">
                @if($reminders->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p>No reminders for this month.</p>
                    </div>
                @else
                    <div class="list-group">
                        @foreach($reminders as $reminder)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $reminder->title }}</div>
                                        <div class="text-muted small">{{ $reminder->starts_at->format('M d, Y h:i A') }}</div>
                                        @if($reminder->remind_at)
                                            <div class="text-muted small">Email: {{ $reminder->remind_at->format('M d, Y h:i A') }}</div>
                                        @endif
                                        @if($reminder->notes)
                                            <p class="mb-0 mt-2">{{ $reminder->notes }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('reminders.destroy', $reminder) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-semibold"><i class="fas fa-list me-2"></i>Upcoming</div>
            <div class="card-body">
                @forelse($upcomingReminders as $reminder)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>{{ $reminder->title }}</span>
                        <span class="text-muted">{{ $reminder->starts_at->format('M d, h:i A') }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">No upcoming reminders.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    if (!params.toString()) return;

    const fields = ['title', 'starts_at', 'remind_at', 'notes'];
    fields.forEach(function (field) {
        const value = params.get(field);
        const input = document.getElementById(field);
        if (input && value) input.value = value;
    });
});
</script>
@endsection
