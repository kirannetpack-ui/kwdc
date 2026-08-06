<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $invoices = Invoice::where('user_id', auth()->id())
            ->where('status', 'unpaid')
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
            'amount' => 'required|numeric|min:1',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        $result = $this->paymentService->initiateKhaltiPayment(
            $request->amount,
            $invoice->invoice_number,
            auth()->id(),
            $invoice->id
        );

        if ($result['success']) {
            // Store transaction
            Transaction::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
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
        $pidx = $request->pidx;

        if (!$pidx) {
            return redirect()->route('payment.failure')
                ->with('error', 'Invalid payment request.');
        }

        $result = $this->paymentService->verifyKhaltiPayment($pidx);

        if ($result['success'] && $result['status'] === 'Completed') {
            // Update transaction
            $transaction = Transaction::where('transaction_id', $pidx)->first();
            if ($transaction) {
                $transaction->markAsCompleted();
                $transaction->payment_details = json_encode($result['data']);
                $transaction->save();
                
                // Mark invoice as paid
                $invoice = Invoice::find($transaction->invoice_id);
                if ($invoice) {
                    $invoice->status = 'paid';
                    $invoice->paid_at = now();
                    $invoice->save();

                    // Send admin notification for payment
                    $this->adminEmailService->notifyPaymentReceived($transaction, $invoice);
                    
                    // Send receipt to customer
                    $this->sendReceipt($transaction, $invoice);
                }
            }
            
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
            'amount' => 'required|numeric|min:1',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        $result = $this->paymentService->initiateEsewaPayment(
            $request->amount,
            $invoice->invoice_number,
            $invoice->id
        );

        if ($result['success']) {
            // Store transaction
            Transaction::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
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
        $pid = $request->pid;
        $refId = $request->refId;
        
        $transaction = Transaction::where('transaction_id', $pid)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'payment_details' => json_encode([
                    'refId' => $refId,
                    'pid' => $pid,
                ]),
            ]);
            
            $invoice = Invoice::find($transaction->invoice_id);
            if ($invoice) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
                $invoice->save();
            }
        }

        return redirect()->route('payment.success')
            ->with('success', 'Payment completed successfully!');
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
}