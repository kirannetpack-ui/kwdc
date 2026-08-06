<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Invoice #{{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <p><strong>Warehouse Request:</strong> #{{ $invoice->warehouse_request_id }}</p>
                    <p><strong>Amount:</strong> Rs. {{ number_format($invoice->amount, 2) }}</p>
                    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 rounded text-xs 
                            @if($invoice->status == 'paid') bg-green-200
                            @elseif($invoice->status == 'pending') bg-yellow-200
                            @else bg-red-200 @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </p>
                    <p><strong>Description:</strong> {{ $invoice->description ?? 'Monthly warehouse rent' }}</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('invoices.client-index') }}" class="text-blue-600">← Back to invoices</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>