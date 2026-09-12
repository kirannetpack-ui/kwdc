<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\AIService;
use App\Services\AdminEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $adminEmailService;

    public function __construct(InvoiceService $invoiceService, AdminEmailService $adminEmailService)
    {
        $this->invoiceService = $invoiceService;
        $this->adminEmailService = $adminEmailService;
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

    private function authorizeInvoice(Invoice $invoice): void
    {
        $user = Auth::user();
        if ($user->isAdmin() || ($user->is_admin ?? false) || $user->role === 'admin') {
            return;
        }
        $isOwner = ($invoice->client_id && $invoice->client_id === $user->id)
            || ($invoice->user_id && $invoice->user_id === $user->id)
            || ($invoice->warehouse_request_id && optional($invoice->warehouseRequest)->client_id === $user->id);
        abort_unless($isOwner, 403, 'Unauthorized access to this invoice.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['client', 'warehouse', 'warehouseRequest.warehouse']);
        return view('invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
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

    /**
     * Store a newly created invoice.
     * (The floating code from lines 105+ is now safely inside this method.)
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'warehouse_request_id' => 'required|exists:warehouse_requests,id',
            'amount'              => 'required|numeric',
        ]);

        $invoice = Invoice::create([
            'warehouse_request_id' => $validated['warehouse_request_id'],
            'amount'               => $validated['amount'],
            'invoice_number'       => 'INV-' . strtoupper(uniqid()),
            'status'               => 'pending',
        ]);

// 🔔 Notify client
    $client = User::find($invoice->client_id);
    if ($client) {
        $this->notificationService->send($client, new InvoiceGeneratedNotification($invoice));
    }
    $this->notificationService->sendToAdmins(new InvoiceGeneratedNotification($invoice));

        // AI Fraud Check
        $aiService = app(AIService::class);
        $anomalyCheck = $aiService->checkInvoiceAnomaly(
            $invoice->total_distance ?? 0,
            $invoice->base_price ?? 0,
            12,
            $invoice->grand_total ?? 0
        );

        if ($anomalyCheck['anomaly'] === true) {
            Log::warning("AI FRAUD DETECTED: " . $anomalyCheck['message'], ['invoice_id' => $invoice->id]);
            $invoice->update(['status' => 'flagged']);
            $this->adminEmailService->notifyAdmin(
                '🚨 Anomaly Detected on Invoice #' . $invoice->id,
                $anomalyCheck['message']
            );
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully');
    }
}
