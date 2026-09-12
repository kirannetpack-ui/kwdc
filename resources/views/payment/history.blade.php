@extends('layouts.app')
@section('title', 'Payment history')
@section('header', 'Payment history')
@section('content')
<h1 class="text-2xl font-semibold mb-8">Payment history</h1>
<div class="overflow-x-auto">
<table class="table w-full">
    <thead><tr><th scope="col">Date</th><th scope="col">Reference</th><th scope="col">Invoice</th><th scope="col">Method</th><th scope="col">Amount</th><th scope="col">Status</th></tr></thead>
    <tbody>
    @forelse($transactions as $transaction)
    <tr><td>{{ ($transaction->payment_date ?? $transaction->created_at)?->format('M j, Y') }}</td><td>{{ $transaction->receipt_no ?? $transaction->transaction_id ?? '#'.$transaction->id }}</td><td>{{ $transaction->invoice?->invoice_number ?? 'Unavailable' }}</td><td>{{ ucfirst($transaction->payment_method ?? 'Other') }}</td><td class="whitespace-nowrap">NPR {{ number_format($transaction->amount, 2) }}</td><td>{{ ucfirst($transaction->status) }}</td></tr>
    @empty
    <tr><td colspan="6"><p class="py-8 text-center">No payments yet.</p></td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-6">{{ $transactions->links() }}</div>
@endsection
