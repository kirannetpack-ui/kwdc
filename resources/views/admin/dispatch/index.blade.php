@extends('layouts.app')

@section('title', 'Dispatch Orders')
@section('header', 'Dispatch Orders')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-200/80">
                        Fleet Operations
                    </span>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $orders->count() }} Total</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">All Dispatch Orders</h2>
                <p class="text-xs text-slate-500 mt-0.5">Centralized oversight of all commercial logistics movements and delivery manifests across Nepal.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dispatch.direct-create') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition inline-flex items-center gap-2 shadow-2xs">
                    <i class="fas fa-plus"></i>
                    <span>New Dispatch</span>
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/80">
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tracking / ID</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Client</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Driver & Fleet</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Route Corridor</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders ?? [] as $order)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <span class="font-extrabold text-slate-900 text-xs block">#{{ $order->id }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $order->tracking_id ?? 'No Tracking' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800 text-xs block">{{ $order->client->name ?? $order->warehouseRequest->client->name ?? 'Standard Shipper' }}</span>
                            <span class="text-[11px] text-slate-400">{{ $order->pickup_contact_phone ?? 'N/A' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($order->driver)
                                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    <i class="fas fa-id-card text-orange-500 text-[11px]"></i>
                                    {{ $order->driver->name }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $order->vehicle->model ?? 'Assigned Unit' }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                    Unassigned
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs text-slate-700 font-medium block truncate max-w-[200px]" title="{{ $order->pickup_address }}">
                                <i class="fas fa-arrow-up text-emerald-500 text-[10px] mr-1"></i>{{ Str::limit($order->pickup_address, 24) }}
                            </span>
                            <span class="text-xs text-slate-700 font-medium block truncate max-w-[200px] mt-0.5" title="{{ $order->delivery_address }}">
                                <i class="fas fa-location-dot text-rose-500 text-[10px] mr-1"></i>{{ Str::limit($order->delivery_address ?? 'Multiple stops', 24) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-extrabold text-slate-900 text-xs block kwdc-kpi-val">रू {{ number_format((float) ($order->grand_total ?? $order->base_price ?? 0)) }}</span>
                            <span class="text-[10px] text-slate-400">{{ $order->total_distance ?? 0 }} km</span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusClasses = [
                                    'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                    'on_the_way' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                    'in_transit' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                    'picked_up' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                                    'assigned' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                ];
                                $badgeClass = $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                {{ str_replace('_', ' ', $order->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('dispatch.show', $order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition border border-slate-200/80">
                                <span>View</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs font-medium">
                            <i class="fas fa-truck-ramp-box text-3xl text-slate-300 block mb-2"></i>
                            No dispatch orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
