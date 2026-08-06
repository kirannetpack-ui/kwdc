<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;
use App\Models\Warehouse;
use App\Models\Stock;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::whereHas('warehouse', function($q) {
            $q->where('owner_id', auth()->id());
        })->orWhere('client_id', auth()->id())
        ->latest()
        ->paginate(20);
        
        return view('client.boxes.index', compact('boxes'));
    }
    
    public function create()
    {
        $warehouses = Warehouse::where('status', 'approved')->get();
        return view('client.boxes.create', compact('warehouses'));
    }
    
public function store(Request $request)
{
    $request->validate([
        'invoice_number' => 'required|string',
        'shipper_name' => 'required|string',
        'warehouse_id' => 'required|exists:warehouses,id',
        'total_boxes' => 'required|integer|min:1|max:100',
        'notes' => 'nullable|string',
        'invoice_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'packing_list_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'insurance_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);
    
    // Generate a unique batch ID for this batch
    $batchId = 'BATCH-' . date('Ymd') . '-' . strtoupper(uniqid());
    
    // Handle file uploads
    $invoicePath = $request->file('invoice_document')->store('boxes/documents/invoices', 'public');
    $packingPath = null;
    $insurancePath = null;
    $otherPaths = [];
    
    if ($request->hasFile('packing_list_document')) {
        $packingPath = $request->file('packing_list_document')->store('boxes/documents/packing_lists', 'public');
    }
    if ($request->hasFile('insurance_document')) {
        $insurancePath = $request->file('insurance_document')->store('boxes/documents/insurance', 'public');
    }
    if ($request->hasFile('other_documents')) {
        foreach ($request->file('other_documents') as $file) {
            $otherPaths[] = $file->store('boxes/documents/others', 'public');
        }
    }
    
    $boxes = [];
    
    for ($i = 1; $i <= $request->total_boxes; $i++) {
        $boxNumber = $i;
        $uniqueBoxId = $batchId . '-BOX-' . str_pad($boxNumber, 3, '0', STR_PAD_LEFT);
        
        $qrData = json_encode([
            'batch' => $batchId,
            'box' => $boxNumber,
            'total' => $request->total_boxes,
            'warehouse_id' => $request->warehouse_id,
            'shipper' => $request->shipper_name,
            'invoice' => $request->invoice_number,
            'date' => now()->format('Y-m-d')
        ]);
        
        $box = Box::create([
            'batch_number' => $uniqueBoxId,  // Unique per box
            'qr_code' => hash('sha256', $qrData . $boxNumber . microtime()),
            'barcode' => 'BAR-' . $batchId . '-' . str_pad($boxNumber, 3, '0', STR_PAD_LEFT),
            'entry_date' => now(),
            'invoice_number' => $request->invoice_number,
            'shipper_name' => $request->shipper_name,
            'warehouse_id' => $request->warehouse_id,
            'total_boxes' => $request->total_boxes,
            'box_number' => $boxNumber,
            'status' => 'pending',
            'notes' => $request->notes,
            'client_id' => auth()->id(),
            'invoice_document' => $invoicePath,
            'packing_list_document' => $packingPath,
            'insurance_document' => $insurancePath,
            'other_documents' => json_encode($otherPaths),
        ]);
        
        $boxes[] = $box;
    }
    
    return redirect()->route('boxes.index')
        ->with('success', "{$request->total_boxes} boxes created successfully. Batch ID: {$batchId}");
}    

    public function getQR($id)
{
    $box = Box::with('warehouse')->findOrFail($id);
    
    return response()->json([
        'qr_data' => $box->generateQRData(),
        'batch_number' => $box->batch_number,
        'box_number' => $box->box_number,
        'total_boxes' => $box->total_boxes,
        'invoice_number' => $box->invoice_number,
        'shipper_name' => $box->shipper_name,
        'warehouse_name' => $box->warehouse->name ?? 'N/A',
        'entry_date' => $box->entry_date->format('Y-m-d'),
        'status' => $box->status,
    ]);
}
    
    public function track($id)
{
    $box = Box::with('warehouse')->findOrFail($id);
    return view('client.boxes.track', compact('box'));
}

    
public function getDocuments($id)
{
    $box = Box::findOrFail($id);
    return response()->json([
        'invoice_document' => $box->invoice_document,
        'packing_list_document' => $box->packing_list_document,
        'insurance_document' => $box->insurance_document,
        'other_documents' => $box->other_documents,
    ]);
}
    public function printLabel($id)
{
    $box = Box::with('warehouse')->findOrFail($id);
    $qrData = $box->generateQRData();
    
    return view('client.boxes.print', compact('box', 'qrData'));
}

}