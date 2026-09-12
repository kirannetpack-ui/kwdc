@extends('layouts.app')

@section('title', 'Warehouse Requests')
@section('header', 'Warehouse Requests')

@push('styles')
<style>
    .kwdc-glass-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="kwdc-glass-card p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-base flex-shrink-0">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Warehouse Requests</h1>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-black bg-orange-100 text-orange-800">
                            {{ $warehouseRequests->total() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Tenant space inquiries and lease authorization queue.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('property.approved') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600 transition">
                    Properties
                </a>
            </div>
        </div>

        @if($warehouseRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Client</th>
                            <th class="py-3 px-3">Warehouse</th>
                            <th class="py-3 px-3">Request Date</th>
                            <th class="py-3 px-3">Duration</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($warehouseRequests as $request)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-3">
                                <a href="{{ route('warehouse-requests.show', $request->id) }}" class="font-bold text-slate-900 hover:text-orange-600 transition">
                                    #{{ $request->id }}
                                </a>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-slate-900">{{ $request->client->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $request->client->email ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-3 font-medium text-slate-700">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-warehouse text-slate-400 text-[11px]"></i>
                                    <span>{{ $request->warehouse->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-600 whitespace-nowrap">
                                @if($request->start_date && $request->end_date)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500">
                                        <i class="far fa-calendar-alt text-slate-400"></i>
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('M d') }} – {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($request->status == 'pending')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                                @elseif($request->status == 'rejected')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">Rejected</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-50 text-slate-700 border border-slate-200">{{ $request->status }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-right whitespace-nowrap">
                                @if($request->status == 'pending')
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <form action="{{ route('property.requests.approve', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-1">
                                                <i class="fas fa-check text-[10px]"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('property.requests.reject', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-1">
                                                <i class="fas fa-times text-[10px]"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Settled</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($warehouseRequests->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $warehouseRequests->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center text-slate-400 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto text-xl">
                    <i class="far fa-clipboard"></i>
                </div>
                <h3 class="font-bold text-slate-700 text-sm">No Pending Requests</h3>
                <p class="text-xs">Incoming client warehouse storage requests will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection
