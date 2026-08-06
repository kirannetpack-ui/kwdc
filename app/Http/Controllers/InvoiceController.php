<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        $invoiceIds = DB::table('invoices')
            ->join('warehouse_requests', 'invoices.warehouse_request_id', '=', 'warehouse_requests.id')
            ->where('warehouse_requests.client_id', $userId)
            ->pluck('invoices.id');
        
        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_invoices' => $invoices->total(),
            'total_amount' => Invoice::whereIn('id', $invoiceIds)->sum('amount'),
            'pending_amount' => Invoice::whereIn('id', $invoiceIds)
                ->where('status', 'pending')
                ->sum('amount'),
        ];
        
        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        return $this->invoiceService->downloadInvoice($invoice);
    }

    public function verify($invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();
        return view('invoices.verify', compact('invoice'));
    }

    public function clientIndex()
    {
        $user = auth()->user();
        $userId = $user->id;
        
        $invoiceIds = DB::table('invoices')
            ->join('warehouse_requests', 'invoices.warehouse_request_id', '=', 'warehouse_requests.id')
            ->where('warehouse_requests.client_id', $userId)
            ->pluck('invoices.id');
        
        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('invoices.client-index', compact('invoices'));
    }

    public function adminIndex(Request $request)
    {
        $query = Invoice::with('client');
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->paginate(30);
        
        return view('admin.invoices.index', compact('invoices'));
    }

    public function markAsPaid(Invoice $invoice, Request $request)
    {
        $invoice->status = 'paid';
        $invoice->save();
        
        return redirect()->back()->with('success', 'Invoice marked as paid successfully');
    }

    public function resendEmail(Invoice $invoice)
    {
        $this->invoiceService->sendInvoiceEmail($invoice);
        return redirect()->back()->with('success', 'Invoice email resent successfully');
    }

$invoice = Invoice::create([...]);

    // 🔥 Run the AI Fraud Check
    $aiService = app(AIService::class);
    $anomalyCheck = $aiService->checkInvoiceAnomaly(
        $invoice->total_distance,
        $invoice->base_price,
        12, // static margin or dynamic margin from DB
        $invoice->grand_total
    );

    if ($anomalyCheck['anomaly'] === true) {
        // Log it for admin to see later
        Log::warning("AI FRAUD DETECTED: " . $anomalyCheck['message'], ['invoice_id' => $invoice->id]);
        // Optional: mark invoice as flagged
        $invoice->update(['status' => 'flagged']);
        
        // Send admin notification
        $this->adminEmailService->notifyAdmin(
            '🚨 Anomaly Detected on Invoice #' . $invoice->id,
            $anomalyCheck['message']
        );
    }

    return redirect()->route('invoices.show', $invoice->id);
}

}