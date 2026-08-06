<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = [
        'batch_number',
        'qr_code',
        'barcode',
        'entry_date',
        'invoice_number',
        'shipper_name',
        'warehouse_id',
        'total_boxes',
        'box_number',
        'status',
        'stock_id',
        'notes',
        'received_by',
        'received_at',
        'client_id',
        'invoice_document',
        'packing_list_document',
        'insurance_document',
        'other_documents'
    ];
    
    protected $casts = [
        'entry_date' => 'date',
        'received_at' => 'datetime',
        'other_documents' => 'array',
    ];
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
    
    // Generate QR data for a box
    public function generateQRData()
    {
        return json_encode([
            'batch' => $this->batch_number,
            'box' => $this->box_number,
            'total' => $this->total_boxes,
            'warehouse' => $this->warehouse->name ?? 'N/A',
            'shipper' => $this->shipper_name,
            'invoice' => $this->invoice_number,
            'date' => $this->entry_date->format('Y-m-d')
        ]);
    }
}