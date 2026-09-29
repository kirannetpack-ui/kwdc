@extends('layouts.app')

@section('title', 'Make Payment | KTM-WDC')
@section('header', 'Make Payment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-[#E8E2D8]">
        <div>
            <h2 class="text-2xl font-serif font-bold text-[#24201D] tracking-tight" style="font-family: 'Newsreader', Georgia, serif;">Settle Pending Invoices</h2>
            <p class="text-sm text-[#645D56] mt-1">Select an outstanding invoice to pay securely using Khalti or eSewa.</p>
        </div>
        <div>
            <a href="{{ route('payment.history') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-[#24201D] bg-[#FFFDF9] border border-[#E8E2D8] hover:bg-[#FAF8F5] transition shadow-2xs">
                <i class="fas fa-history text-xs text-[#D96B43]"></i>
                <span>Payment History</span>
            </a>
        </div>
    </div>

    <!-- Feedback Banner Slot -->
    <div id="paymentAlert" class="hidden rounded-xl p-4 text-sm font-medium transition-all duration-200"></div>

    @if(session('error'))
        <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Invoices List -->
    <div class="bg-[#FFFDF9] border border-[#E8E2D8] rounded-2xl shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E8E2D8] bg-[#FAF8F5] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-receipt text-[#D96B43]"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-[#645D56]">Outstanding Invoices</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#D96B43]/10 text-[#D96B43] border border-[#D96B43]/20">
                {{ $invoices->count() }} Pending
            </span>
        </div>

        <div class="p-6">
            @if($invoices->count() > 0)
                <div class="space-y-4">
                    @foreach($invoices as $invoice)
                    @php
                        $invAmount = (float) ($invoice->grand_total ?? $invoice->amount ?? 0);
                        $dueDate = $invoice->payment_due_date ?? $invoice->due_date;
                    @endphp
                    <div class="border border-[#E8E2D8] rounded-xl p-5 bg-[#FAF8F5]/50 hover:bg-[#FAF8F5] transition flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-sm font-bold text-[#24201D]">
                                    {{ $invoice->invoice_number ?? ('INV-' . $invoice->id) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            </div>

                            <p class="text-xs text-[#645D56]">
                                <i class="fas fa-calendar-alt mr-1 text-[#8C827A]"></i>
                                Due: {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('M d, Y') : 'Due upon receipt' }}
                            </p>

                            <div class="text-lg font-bold text-[#D96B43] pt-1">
                                रू {{ number_format($invAmount, 2) }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Khalti Button -->
                            <button type="button" 
                                    onclick="payWithKhalti({{ $invoice->id }}, {{ $invAmount }})" 
                                    id="khalti-btn-{{ $invoice->id }}"
                                    class="btn-khalti inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                                <i class="fas fa-wallet text-sm text-white"></i>
                                <span class="text-white">Pay with Khalti</span>
                            </button>

                            <!-- eSewa Button -->
                            <form action="{{ route('payment.esewa.init') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                <input type="hidden" name="amount" value="{{ $invAmount }}">
                                <button type="submit" 
                                        class="btn-esewa inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                                    <i class="fas fa-money-bill-wave text-sm text-white"></i>
                                    <span class="text-white">Pay with eSewa</span>
                                </button>
                            </form>

                            <!-- View Details -->
                            <a href="{{ route('invoices.show', $invoice->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-[#645D56] hover:text-[#24201D] bg-[#FFFDF9] border border-[#E8E2D8] hover:bg-[#FAF8F5] transition">
                                <i class="fas fa-eye text-xs"></i>
                                <span>Details</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-2xl">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-[#24201D]">All Invoices Settled</h3>
                        <p class="text-xs text-[#645D56]">You do not have any pending invoices requiring settlement right now.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#D96B43] hover:bg-[#C35832] text-white text-xs font-semibold shadow-sm transition">
                            <i class="fas fa-arrow-left"></i>
                            <span>Return to Dashboard</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showAlert(message, type = 'error') {
    const alertBox = document.getElementById('paymentAlert');
    if (!alertBox) return;
    
    alertBox.className = type === 'error' 
        ? 'rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-3 transition-all duration-200' 
        : 'rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 transition-all duration-200';
    
    const icon = type === 'error' ? 'fa-exclamation-circle text-red-500' : 'fa-check-circle text-emerald-500';
    alertBox.innerHTML = `<i class="fas ${icon} text-base"></i><span>${message}</span>`;
    alertBox.classList.remove('hidden');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function payWithKhalti(invoiceId, amount) {
    const btn = document.getElementById('khalti-btn-' + invoiceId);
    if (!btn) return;

    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Initializing...';

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.content : '';

    fetch('{{ route("payment.khalti.init") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            invoice_id: invoiceId,
            amount: amount
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Payment initiation failed with server error.');
        }
        return data;
    })
    .then(data => {
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            showAlert(data.message || 'Unable to initiate Khalti checkout. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Khalti checkout error:', error);
        showAlert(error.message || 'Network error during checkout initialization. Please verify your connection.');
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
}
</script>
@endsection