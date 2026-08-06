<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseRequest;
use App\Models\DispatchOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function downloadWarehouse($id)
    {
        $warehouse = Warehouse::with('user')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.warehouse', compact('warehouse'));
        return $pdf->download("Warehouse_{$warehouse->id}.pdf");
    }

    public function downloadDispatch($id)
    {
        $dispatch = DispatchOrder::with(['client', 'driver', 'stops'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.dispatch', compact('dispatch'));
        return $pdf->download("Dispatch_{$dispatch->tracking_id}.pdf");
    }

    public function downloadInvoice($id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        return $pdf->download("Invoice_{$invoice->invoice_number}.pdf");
    }
}