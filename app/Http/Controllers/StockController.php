<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StockController extends Controller
{
  
    public function index()
    {
        $stocks = Stock::where('client_code', auth()->user()->user_code)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('stock.index', compact('stocks'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'approved')->get();
        return view('stock.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'number_of_boxes' => 'required|integer|min:1',
            'quantity_per_box' => 'required|integer|min:1',
            'invoice_number' => 'nullable|string|max:100',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $client = auth()->user();
        $totalQuantity = $request->number_of_boxes * $request->quantity_per_box;
        
        $batchId = Stock::generateBatchId($client->user_code);
        $sku = Stock::generateSKU();
        
        // Handle file upload
        $invoicePath = null;
        if ($request->hasFile('invoice_file')) {
            $invoiceFile = $request->file('invoice_file');
            $invoiceFileName = time() . '_' . uniqid() . '.' . $invoiceFile->getClientOriginalExtension();
            $invoicePath = $invoiceFile->storeAs('stock_documents/invoices', $invoiceFileName, 'public');
        }
        
        $warehouseName = null;
        if ($request->warehouse_id) {
            $warehouse = Warehouse::find($request->warehouse_id);
            $warehouseName = $warehouse->name ?? null;
        }
        
        // Prepare QR Code Data
        $qrData = [
            'batch_id' => $batchId,
            'sku' => $sku,
            'product_name' => $request->product_name,
            'number_of_boxes' => $request->number_of_boxes,
            'quantity_per_box' => $request->quantity_per_box,
            'total_quantity' => $totalQuantity,
            'unit' => $request->unit,
            'invoice_number' => $request->invoice_number,
            'warehouse_id' => $request->warehouse_id,
            'warehouse_name' => $warehouseName,
            'client_id' => $client->user_code,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date' => $request->expiry_date,
            'received_date' => now()->format('Y-m-d'),
            'received_by' => $client->name,
            'status' => 'in_stock'
        ];
        
        // Generate QR Code using SVG format (no Imagick needed)
        $qrCodeFileName = $batchId . '.svg';
        $qrCodePath = 'qr_codes/' . $qrCodeFileName;
        
        $qrCode = QrCode::format('svg')
            ->size(400)
            ->errorCorrection('H')
            ->margin(2)
            ->generate(json_encode($qrData));
        
        Storage::disk('public')->put($qrCodePath, $qrCode);
        
        // Create stock record
        $stock = Stock::create([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'unit' => $request->unit,
            'number_of_boxes' => $request->number_of_boxes,
            'quantity_per_box' => $request->quantity_per_box,
            'total_quantity' => $totalQuantity,
            'batch_id' => $batchId,
            'sku' => $sku,
            'invoice_number' => $request->invoice_number,
            'invoice_file_path' => $invoicePath,
            'warehouse_id' => $request->warehouse_id,
            'warehouse_name' => $warehouseName,
            'client_code' => $client->user_code,
            'client_name' => $client->name,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date' => $request->expiry_date,
            'received_date' => now(),
            'qr_code_path' => $qrCodePath,
            'qr_code_data' => json_encode($qrData),
            'status' => 'in_stock',
            'remaining_quantity' => $totalQuantity,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
        ]);
        
        return redirect()
            ->route('stock.show', $stock->id)
            ->with('success', 'Stock added successfully! Batch ID: ' . $batchId);
    }

    public function show($id)
    {
        $stock = Stock::where('id', $id)
            ->where('client_code', auth()->user()->user_code)
            ->firstOrFail();
        
        return view('stock.show', compact('stock'));
    }
    
    public function viewQrCode($id)
    {
        $stock = Stock::where('id', $id)
            ->where('client_code', auth()->user()->user_code)
            ->firstOrFail();
        
        if (Storage::disk('public')->exists($stock->qr_code_path)) {
            $file = Storage::disk('public')->path($stock->qr_code_path);
            $mimeType = Storage::disk('public')->mimeType($stock->qr_code_path);
            return response()->file($file, ['Content-Type' => $mimeType]);
        }
        
        return redirect()->back()->with('error', 'QR Code not found.');
    }
    
    public function downloadQrCode($id)
    {
        $stock = Stock::where('id', $id)
            ->where('client_code', auth()->user()->user_code)
            ->firstOrFail();
        
        if (Storage::disk('public')->exists($stock->qr_code_path)) {
            $extension = pathinfo($stock->qr_code_path, PATHINFO_EXTENSION);
            $filename = $stock->batch_id . '.' . $extension;
            return Storage::disk('public')->download($stock->qr_code_path, $filename);
        }
        
        return redirect()->back()->with('error', 'QR Code not found.');
    }
    
    public function downloadDocument($id, $type)
    {
        $stock = Stock::where('id', $id)
            ->where('client_code', auth()->user()->user_code)
            ->firstOrFail();
        
        $path = null;
        if ($type === 'invoice') {
            $path = $stock->invoice_file_path;
        }
        
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }
        
        return redirect()->back()->with('error', 'Document not found.');
    }
}