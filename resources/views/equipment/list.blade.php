@extends('layouts.app')

@section('title', 'Fleet Equipment List')
@section('header', 'Fleet Machinery & Equipment')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Commercial Equipment Fleet</h2>
            <p class="text-xs text-slate-500">Manage registered forklifts, cranes, excavators, and construction machinery available for lease.</p>
        </div>
        <a href="{{ route('equipment.register') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm transition whitespace-nowrap">
            <i class="fas fa-plus-circle"></i>
            <span>Register Equipment</span>
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-3.5">Equipment / Model</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Base Location</th>
                        <th class="px-6 py-3.5">Daily Rate</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($equipment ?? [] as $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4">
                            <strong class="text-slate-900 font-bold block">{{ $item->name }}</strong>
                            <span class="text-xs text-slate-500">{{ $item->model ? 'Model: ' . $item->model : 'Standard Fleet' }} &bull; Year {{ $item->year ?? '2022' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 uppercase tracking-wide">
                                {{ ucfirst($item->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            {{ $item->location ?: 'Kathmandu Valley' }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900">
                            NPR {{ number_format((float) ($item->daily_rate ?? 3500), 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
                                @if($item->status == 'available') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($item->status == 'in_use') bg-blue-50 text-blue-700 border border-blue-200
                                @elseif($item->status == 'maintenance') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ ucfirst(str_replace('_', ' ', $item->status ?? 'Available')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('equipment.edit', $item->id) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 text-xs font-bold transition" title="Edit Machinery">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('equipment.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this equipment from active registry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600 text-xs font-bold transition" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                            <i class="fas fa-tractor text-4xl mb-3 text-slate-300 block"></i>
                            No equipment registered in your fleet yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
