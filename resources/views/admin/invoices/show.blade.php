<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Invoice #{{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <p><strong>Client:</strong> {{ $invoice->warehouseRequest->client->name }} ({{ $invoice->warehouseRequest->client->email }})</p>
                    <p><strong>Warehouse Request:</strong> #{{ $invoice->warehouse_request_id }}</p>
                    <p><strong>Amount:</strong> Rs. {{ number_format($invoice->amount, 2) }}</p>
                    <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 rounded text-xs 
                            @if($invoice->status == 'paid') bg-green-200 text-green-800
                            @elseif($invoice->status == 'pending') bg-yellow-200 text-yellow-800
                            @else bg-red-200 text-red-800 @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </p>
                    <p><strong>Description:</strong> {{ $invoice->description ?? 'Monthly warehouse rent' }}</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('admin.invoices') }}" class="text-blue-600">← Back to all invoices</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>