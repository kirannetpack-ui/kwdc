@extends('layouts.app')

@section('title', 'Reminder Calendar')
@section('header', 'Reminder Calendar')

@section('content')
@php
    $gridStart = $month->copy()->startOfWeek(1);
    $byDay = $reminders->groupBy(fn ($event) => $event->starts_at->toDateString());
    $calendarEvents = $reminders->map(function ($event) {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'starts_at' => $event->starts_at->format('Y-m-d\TH:i'),
            'remind_at' => $event->remind_at?->format('Y-m-d\TH:i'),
            'notes' => $event->notes,
            'url' => route('reminders.update', $event),
        ];
    })->values();
@endphp
<section class="calendar-main mb-5">
    <div class="calendar-toolbar">
        <h2>{{ $month->format('F Y') }}</h2>
        <nav class="d-flex gap-2" aria-label="Calendar month">
            <a class="btn btn-light" title="Previous month" aria-label="Previous month" href="{{ route('reminders.index', ['month' => $month->copy()->subMonth()->toDateString()]) }}"><i class="fas fa-chevron-left"></i></a>
            <a class="btn btn-light" href="{{ route('reminders.index') }}">Today</a>
            <a class="btn btn-light" title="Next month" aria-label="Next month" href="{{ route('reminders.index', ['month' => $month->copy()->addMonth()->toDateString()]) }}"><i class="fas fa-chevron-right"></i></a>
        </nav>
    </div>
    <div class="calendar-weekdays">@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)<div>{{ $day }}</div>@endforeach</div>
    <div class="calendar-grid">
    @for($i = 0; $i < 42; $i++)
        @php $date = $gridStart->copy()->addDays($i); @endphp
        <div class="calendar-day {{ $date->month !== $month->month ? 'outside' : '' }} {{ $date->isToday() ? 'today' : '' }}">
            <button type="button" class="calendar-date" data-calendar-date="{{ $date->toDateString() }}" aria-label="Add reminder on {{ $date->format('F j, Y') }}">{{ $date->day }}</button>
            @foreach($byDay->get($date->toDateString(), collect()) as $event)
            <button type="button" class="calendar-event" data-event-id="{{ $event->id }}" title="{{ $event->title }}"><time>{{ $event->starts_at->format('H:i') }}</time> {{ $event->title }}</button>
            @endforeach
        </div>
    @endfor
    </div>
</section>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-semibold">
                <span id="editor-heading">New reminder</span>
                <button type="button" class="btn btn-sm btn-light float-end" id="new-reminder" aria-label="New reminder"><i class="fas fa-plus"></i></button>
            </div>
            <div class="card-body">
                <form id="reminder-editor" method="POST" action="{{ route('reminders.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="reminder-method" value="POST">
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
                                        <button type="button" class="btn btn-link p-0 text-start" data-event-id="{{ $reminder->id }}">{{ $reminder->title }}</button>
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
    const events = @json($calendarEvents);
    const form = document.getElementById('reminder-editor');
    function edit(event = null) {
        form.action = event?.url || @json(route('reminders.store'));
        document.getElementById('reminder-method').value = event ? 'PUT' : 'POST';
        document.getElementById('editor-heading').textContent = event ? 'Edit reminder' : 'New reminder';
        ['title', 'starts_at', 'remind_at', 'notes'].forEach(key => document.getElementById(key).value = event?.[key] || '');
    }
    document.querySelectorAll('[data-event-id]').forEach(button => button.addEventListener('click', () => {
        edit(events.find(event => event.id === Number(button.dataset.eventId)));
        document.getElementById('title').focus();
    }));
    document.querySelectorAll('[data-calendar-date]').forEach(button => button.addEventListener('click', () => {
        edit(); document.getElementById('starts_at').value = button.dataset.calendarDate + 'T09:00';
        document.getElementById('title').focus();
    }));
    document.getElementById('new-reminder').addEventListener('click', () => edit());
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
