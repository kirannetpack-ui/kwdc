<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'order_type', 'order_id', 'client_id', 'warehouse_id',
        'subtotal', 'discount', 'tax_rate', 'tax_amount', 'grand_total',
        'payment_status', 'payment_method', 'payment_due_date', 'paid_at',
        'billing_type', 'pan_number', 'billing_address', 'notes', 'items', 'qr_code'
    ];

    protected $casts = [
        'items' => 'array',
        'payment_due_date' => 'date',
        'paid_at' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function order(): MorphTo
    {
        return $this->morphTo('order', 'order_type', 'order_id');
    }

    // Generate invoice number automatically
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month)
                          ->orderBy('id', 'desc')
                          ->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "INV-{$year}{$month}-{$newNumber}";
    }

    // Mark invoice as paid
    public function markAsPaid($paymentMethod = null)
    {
        $this->payment_status = 'paid';
        $this->payment_method = $paymentMethod;
        $this->paid_at = now();
        $this->save();
        
        return $this;
    }

    // Check if invoice is overdue
    public function isOverdue(): bool
    {
        return $this->payment_status == 'unpaid' && now()->gt($this->payment_due_date);
    }
    
    // Get status badge class
    public function getStatusBadgeClass(): string
    {
        if ($this->payment_status == 'paid') {
            return 'badge-success';
        } elseif ($this->isOverdue()) {
            return 'badge-danger';
        }
        return 'badge-warning';
    }
}