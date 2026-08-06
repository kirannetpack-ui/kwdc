@extends('layouts.app')

@section('title', 'Make Payment')
@section('header', 'Make Payment')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i>
                    Select Invoice to Pay
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        @foreach($invoices as $invoice)
                        <div class="border rounded-lg p-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="font-bold">Invoice #{{ $invoice->invoice_number }}</h5>
                                    <p class="text-muted small">Due Date: {{ $invoice->payment_due_date->format('F d, Y') }}</p>
                                    <p class="text-muted small">Amount: रू {{ number_format($invoice->amount, 2) }}</p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button onclick="payWithKhalti({{ $invoice->id }}, {{ $invoice->amount }})" 
                                            class="btn btn-primary me-2">
                                        <img src="{{ asset('images/khalti.png') }}" style="height: 20px; display: inline-block;">
                                        Pay with Khalti
                                    </button>
                                    <form action="{{ route('payment.esewa.init') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                        <input type="hidden" name="amount" value="{{ $invoice->amount }}">
                                        <button type="submit" class="btn btn-success">
                                            <img src="{{ asset('images/esewa.png') }}" style="height: 20px; display: inline-block;">
                                            Pay with eSewa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                            <p class="text-muted">No pending invoices to pay</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-orange mt-3">
                                <i class="fas fa-home me-2"></i>Go to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payWithKhalti(invoiceId, amount) {
    // Show loading
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch('{{ route("payment.khalti.init") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            invoice_id: invoiceId,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to Khalti payment page
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'Payment initiation failed');
            btn.disabled = false;
            btn.innerHTML = 'Pay with Khalti';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
        btn.disabled = false;
        btn.innerHTML = 'Pay with Khalti';
    });
}
</script>
@endsection