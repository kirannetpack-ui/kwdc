@extends('layouts.app')

@section('title', 'Reminder Calendar')
@section('header', 'Reminder Calendar')

@push('styles')
<style>
    .kwdc-cal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .calendar-toolbar {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .calendar-toolbar h2 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.025em;
    }
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 10px 0;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f1f5f9;
        gap: 1px;
    }
    .calendar-day {
        background: #ffffff;
        min-height: 105px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: background 0.15s ease;
    }
    .calendar-day:hover {
        background: #fafafa;
    }
    .calendar-day.outside {
        background: #f8fafc;
        opacity: 0.45;
    }
    .calendar-day.today {
        background: #fffbf5;
    }
    .calendar-date {
        border: none;
        background: transparent;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        margin-bottom: 2px;
        transition: all 0.15s ease;
    }
    .calendar-date:hover {
        background: #e2e8f0;
    }
    .calendar-day.today .calendar-date {
        background: #f97316;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.4);
    }
    .calendar-event {
        border: 1px solid #ffedd5;
        border-radius: 8px;
        padding: 3px 6px;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        background: #fff7ed;
        color: #c2410c;
        display: flex;
        align-items: center;
        gap: 4px;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.3;
    }
    .calendar-event:hover {
        background: #ffedd5;
        border-color: #fed7aa;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(249, 115, 22, 0.12);
    }
    .calendar-event time {
        font-size: 10px;
        font-weight: 700;
        color: #ea580c;
        flex-shrink: 0;
    }
    .kwdc-cal-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .kwdc-cal-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .kwdc-cal-btn.icon-only {
        width: 34px;
        padding: 0;
    }
</style>
@endpush

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

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Interactive Calendar Grid Card -->
    <section class="kwdc-cal-card">
        <div class="calendar-toolbar">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-base">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2>{{ $month->format('F Y') }}</h2>
            </div>
            <nav class="flex items-center gap-2" aria-label="Calendar month">
                <a class="kwdc-cal-btn icon-only" title="Previous month" aria-label="Previous month" href="{{ route('reminders.index', ['month' => $month->copy()->subMonth()->toDateString()]) }}">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a class="kwdc-cal-btn" href="{{ route('reminders.index') }}">Today</a>
                <a class="kwdc-cal-btn icon-only" title="Next month" aria-label="Next month" href="{{ route('reminders.index', ['month' => $month->copy()->addMonth()->toDateString()]) }}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>
        </div>

        <div class="calendar-weekdays">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                <div>{{ $day }}</div>
            @endforeach
        </div>

        <div class="calendar-grid">
            @for($i = 0; $i < 42; $i++)
                @php $date = $gridStart->copy()->addDays($i); @endphp
                <div class="calendar-day {{ $date->month !== $month->month ? 'outside' : '' }} {{ $date->isToday() ? 'today' : '' }}">
                    <button type="button" class="calendar-date" data-calendar-date="{{ $date->toDateString() }}" aria-label="Add reminder on {{ $date->format('F j, Y') }}">
                        {{ $date->day }}
                    </button>
                    @foreach($byDay->get($date->toDateString(), collect()) as $event)
                        <button type="button" class="calendar-event" data-event-id="{{ $event->id }}" title="{{ $event->title }}">
                            <time>{{ $event->starts_at->format('H:i') }}</time>
                            <span class="truncate">{{ $event->title }}</span>
                        </button>
                    @endforeach
                </div>
            @endfor
        </div>
    </section>

    <!-- Bottom Action & Schedule Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- New / Edit Reminder Form Card -->
        <div class="kwdc-cal-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-pen-to-square text-orange-500"></i>
                    <h3 class="font-extrabold text-slate-900 text-sm" id="editor-heading">New Reminder</h3>
                </div>
                <button type="button" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition" id="new-reminder" aria-label="New reminder">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <form id="reminder-editor" method="POST" action="{{ route('reminders.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="reminder-method" value="POST">
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1" for="title">Title</label>
                    <input class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" id="title" name="title" value="{{ old('title') }}" placeholder="Call driver, review invoice..." required>
                    @error('title') <div class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1" for="starts_at">Date & Time</label>
                    <input class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required>
                    @error('starts_at') <div class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1" for="remind_at">Email Notification (Optional)</label>
                    <input class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" id="remind_at" name="remind_at" type="datetime-local" value="{{ old('remind_at') }}">
                    @error('remind_at') <div class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1" for="notes">Notes</label>
                    <textarea class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition" id="notes" name="notes" rows="3" placeholder="Additional details...">{{ old('notes') }}</textarea>
                    @error('notes') <div class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <button class="w-full py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2" type="submit">
                    <i class="fas fa-check"></i>
                    <span>Save Reminder</span>
                </button>
            </form>
        </div>

        <!-- Reminders List Card -->
        <div class="lg:col-span-2 kwdc-cal-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-list-check text-blue-500"></i>
                    <h3 class="font-extrabold text-slate-900 text-sm">Scheduled Events for {{ $month->format('F Y') }}</h3>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $reminders->count() }} total</span>
            </div>

            @if($reminders->isEmpty())
                <div class="py-12 text-center text-slate-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto text-xl">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <p class="text-xs font-medium">No reminders scheduled for this month.</p>
                </div>
            @else
                <div class="space-y-2.5 max-h-[460px] overflow-y-auto pr-1">
                    @foreach($reminders as $reminder)
                        <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-slate-200 transition flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <button type="button" class="text-left font-bold text-slate-900 text-xs hover:text-orange-600 transition" data-event-id="{{ $reminder->id }}">
                                    {{ $reminder->title }}
                                </button>
                                <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                    <span><i class="far fa-clock text-slate-400 mr-1"></i>{{ $reminder->starts_at->format('M d, Y h:i A') }}</span>
                                    @if($reminder->remind_at)
                                        <span><i class="far fa-bell text-amber-500 mr-1"></i>Email: {{ $reminder->remind_at->format('M d, h:i A') }}</span>
                                    @endif
                                </div>
                                @if($reminder->notes)
                                    <p class="text-[11px] text-slate-600 pt-1">{{ $reminder->notes }}</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('reminders.destroy', $reminder) }}">
                                @csrf
                                @method('DELETE')
                                <button class="w-7 h-7 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center text-xs transition" type="submit" title="Delete reminder">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const events = @json($calendarEvents);
    const form = document.getElementById('reminder-editor');
    function edit(event = null) {
        form.action = event?.url || @json(route('reminders.store'));
        document.getElementById('reminder-method').value = event ? 'PUT' : 'POST';
        document.getElementById('editor-heading').textContent = event ? 'Edit Reminder' : 'New Reminder';
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
@endpush
