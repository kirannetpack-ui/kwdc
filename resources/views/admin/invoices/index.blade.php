@extends('layouts.app')

@section('title', 'Invoices')
@section('header', 'Invoice Management')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">All Invoices</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices ?? [] as $invoice)
                <tr>
                    <td class="px-6 py-4">#{{ $invoice->id }}</td>
                    <td class="px-6 py-4">{{ $invoice->client->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">रु {{ number_format($invoice->amount ?? 0, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status ?? 'Pending') }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $invoice->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No invoices found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection