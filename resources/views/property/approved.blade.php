@extends('layouts.app')

@section('title', 'Approved Properties')
@section('header', 'Approved Warehouse Facilities')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Approved Commercial Facilities</h2>
            <p class="text-xs text-slate-500">Certified active warehouse properties ready for enterprise leasing and cargo storage.</p>
        </div>
        <a href="{{ route('warehouses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm transition whitespace-nowrap">
            <i class="fas fa-plus-circle"></i>
            <span>Register New Facility</span>
        </a>
    </div>

    @if($approvedWarehouses->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3.5">Facility Name</th>
                            <th class="px-6 py-3.5">Location</th>
                            <th class="px-6 py-3.5">Usable Area</th>
                            <th class="px-6 py-3.5">Monthly Rate</th>
                            <th class="px-6 py-3.5">Approved Date</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($approvedWarehouses as $warehouse)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <strong class="text-slate-900 font-bold block">{{ $warehouse->name }}</strong>
                                <span class="text-xs text-slate-500">
                                    Owner: {{ optional($warehouse->user)->name ?? optional($warehouse->owner)->name ?? 'Verified Partner' }}
                                    @if($warehouse->cold_storage) &bull; <span class="text-blue-600 font-semibold">Cold Storage</span> @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                                {{ \Illuminate\Support\Str::limit($warehouse->address ?: $warehouse->location, 32) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ number_format((float) $warehouse->area_sqft) }} sq ft
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-orange-600">
                                NPR {{ number_format((float) $warehouse->price_per_sqft, 2) }} / sq ft
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $warehouse->approved_at ? $warehouse->approved_at->format('M d, Y') : 'Certified' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('property.warehouses.show', $warehouse->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition" title="View Details">
                                        <i class="fas fa-eye text-blue-600"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('warehouses.pdf', $warehouse->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-bold transition" title="Download Official Certificate PDF">
                                        <i class="fas fa-file-pdf text-orange-600"></i>
                                        <span>Certificate</span>
                                    </a>
                                    <a href="{{ route('property.warehouses.edit', $warehouse->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Edit Facility">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($approvedWarehouses, 'links'))
            <div class="p-4 border-t border-slate-100">
                {{ $approvedWarehouses->links() }}
            </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400">
            <i class="fas fa-warehouse text-4xl mb-3 text-slate-300 block"></i>
            <h3 class="text-base font-bold text-slate-700 mb-1">No Approved Facilities Yet</h3>
            <p class="text-xs text-slate-500 mb-4">Register your commercial warehouse space to receive verification and client rental requests.</p>
            <a href="{{ route('warehouses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs transition">
                <i class="fas fa-plus-circle"></i> Register Facility
            </a>
        </div>
    @endif
</div>
@endsection
