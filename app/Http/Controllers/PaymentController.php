<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\AdminEmailService;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $adminEmailService;

    public function __construct(PaymentService $paymentService, AdminEmailService $adminEmailService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
        $this->adminEmailService = $adminEmailService;
    }

    /**
     * Show payment page
     */
    public function index()
    {
        $invoices = Invoice::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('client_id', auth()->id());
            })
            ->where('payment_status', 'unpaid')
            ->get();
        
        return view('payment.index', compact('invoices'));
    }

    /**
     * Initialize Khalti payment
     */
    public function khaltiInit(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'nullable|numeric|min:1',
        ]);

        $invoice = $this->payableInvoice((int) $request->invoice_id);
        $amount = $this->invoicePayableAmount($invoice);

        if ($request->filled('amount') && round((float) $request->amount, 2) !== $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount does not match the invoice total.',
            ], 422);
        }
        
        $result = $this->paymentService->initiateKhaltiPayment(
            $amount,
            $invoice->invoice_number,
            auth()->id(),
            $invoice->id
        );

        if ($result['success']) {
            // Store transaction
            Transaction::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'payment_method' => 'khalti',
                'transaction_id' => $result['data']['pidx'],
                'status' => 'pending',
                'payment_details' => json_encode($result['data']),
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['data']['payment_url'],
                'pidx' => $result['data']['pidx'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Payment initiation failed',
        ], 500);
    }

    /**
     * Verify Khalti payment
     */
    public function khaltiVerify(Request $request)
    {
        $pidx = trim((string) $request->input('pidx'));

        if (!$pidx) {
            return redirect()->route('payment.failure')
                ->with('error', 'Invalid payment request.');
        }

        $transaction = Transaction::where('transaction_id', $pidx)
            ->where('user_id', auth()->id())
            ->where('payment_method', 'khalti')
            ->first();

        if (!$transaction) {
            return redirect()->route('payment.failure')
                ->with('error', 'Payment transaction was not found.');
        }

        if ($transaction->isCompleted()) {
            return redirect()->route('payment.success')
                ->with('success', 'Payment already completed.');
        }

        if (!$transaction->isPending()) {
            return redirect()->route('payment.failure')
                ->with('error', 'Payment transaction is no longer payable.');
        }

        $result = $this->paymentService->verifyKhaltiPayment($pidx);

        if ($result['success'] && $result['status'] === 'Completed') {
            $providerAmount = ((float) Arr::get($result, 'data.total_amount', 0)) / 100;
            if ($providerAmount && round($providerAmount, 2) !== round((float) $transaction->amount, 2)) {
                $transaction->markAsFailed('Provider amount did not match the invoice transaction amount.');

                return redirect()->route('payment.failure')
                    ->with('error', 'Payment amount mismatch. Please contact support.');
            }

            $invoice = Invoice::where('id', $transaction->invoice_id)
                ->where('payment_status', 'unpaid')
                ->first();

            if (!$invoice) {
                $transaction->markAsFailed('Invoice is missing or no longer payable.');

                return redirect()->route('payment.failure')
                    ->with('error', 'Payment invoice is no longer payable.');
            }

            $this->completeVerifiedTransaction($transaction, $invoice, 'khalti', $result['data']);
            
            return redirect()->route('payment.success')
                ->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('payment.failure')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * Send payment receipt to customer
     */
    private function sendReceipt($transaction, $invoice)
    {
        try {
            \Mail::send('emails.payment-receipt', [
                'transaction' => $transaction,
                'invoice' => $invoice,
            ], function ($message) use ($transaction, $invoice) {
                $message->to($transaction->user->email, $transaction->user->name)
                        ->subject('Payment Receipt - ' . $invoice->invoice_number);
            });
        } catch (\Exception $e) {
            \Log::error('Receipt email failed: ' . $e->getMessage());
        }
    }

    /**
     * eSewa Payment Initiation
     */
    public function esewaInit(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'nullable|numeric|min:1',
        ]);

        $invoice = $this->payableInvoice((int) $request->invoice_id);
        $amount = $this->invoicePayableAmount($invoice);

        if ($request->filled('amount') && round((float) $request->amount, 2) !== $amount) {
            return redirect()->back()
                ->with('error', 'Payment amount does not match the invoice total.');
        }
        
        $result = $this->paymentService->initiateEsewaPayment(
            $amount,
            $invoice->invoice_number,
            $invoice->id
        );

        if ($result['success']) {
            // Store transaction
            Transaction::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'payment_method' => 'esewa',
                'transaction_id' => $result['pid'],
                'status' => 'pending',
            ]);

            return redirect($result['url']);
        }

        return redirect()->back()
            ->with('error', 'Payment initiation failed. Please try again.');
    }

    /**
     * eSewa Success Callback
     */
    public function esewaSuccess(Request $request)
    {
        $pid = trim((string) $request->input('pid'));
        $refId = trim((string) $request->input('refId'));

        if (!$pid || !$refId) {
            return redirect()->route('payment.failure')
                ->with('error', 'Invalid payment request.');
        }
        
        $transaction = Transaction::where('transaction_id', $pid)
            ->where('user_id', auth()->id())
            ->where('payment_method', 'esewa')
            ->first();
        
        if (!$transaction) {
            return redirect()->route('payment.failure')
                ->with('error', 'Payment transaction was not found.');
        }

        if ($transaction->isCompleted()) {
            return redirect()->route('payment.success')
                ->with('success', 'Payment already completed.');
        }

        if (!$transaction->isPending()) {
            return redirect()->route('payment.failure')
                ->with('error', 'Payment transaction is no longer payable.');
        }

        $verification = $this->paymentService->verifyEsewaPayment(
            $pid,
            $refId,
            (float) $transaction->amount
        );

        if ($verification['success'] ?? false) {
            $invoice = Invoice::where('id', $transaction->invoice_id)
                ->where('payment_status', 'unpaid')
                ->first();

            if (!$invoice) {
                $transaction->markAsFailed('Invoice is missing or no longer payable.');

                return redirect()->route('payment.failure')
                    ->with('error', 'Payment invoice is no longer payable.');
            }

            $this->completeVerifiedTransaction($transaction, $invoice, 'esewa', $verification['data'] ?? [
                'refId' => $refId,
                'pid' => $pid,
            ]);

            return redirect()->route('payment.success')
                ->with('success', 'Payment completed successfully!');
        }

        $transaction->markAsFailed($verification['message'] ?? 'eSewa verification failed.');

        return redirect()->route('payment.failure')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * eSewa Failure Callback
     */
    public function esewaFailure(Request $request)
    {
        return redirect()->route('payment.failure')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Success Page
     */
    public function success()
    {
        return view('payment.success');
    }

    /**
     * Failure Page
     */
    public function failure()
    {
        return view('payment.failure');
    }

    /**
     * Get payment history
     */
    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('invoice')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('payment.history', compact('transactions'));
    }

    private function payableInvoice(int $invoiceId): Invoice
    {
        return Invoice::where('id', $invoiceId)
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('client_id', auth()->id());
            })
            ->where('payment_status', 'unpaid')
            ->firstOrFail();
    }

    private function invoicePayableAmount(Invoice $invoice): float
    {
        $amount = $invoice->grand_total ?: ($invoice->amount ?? 0);

        return round((float) $amount, 2);
    }

    private function completeVerifiedTransaction(Transaction $transaction, Invoice $invoice, string $method, array $details): void
    {
        $transaction->markAsCompleted();
        $transaction->payment_details = $details;
        $transaction->save();

        $invoice->markAsPaid($method);

        $this->adminEmailService->notifyPaymentReceived($transaction, $invoice);
        $this->sendReceipt($transaction, $invoice);
    }
}
