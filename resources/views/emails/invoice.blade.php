<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #e53e3e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background: #f9f9f9;
        }
        .invoice-details {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #e53e3e;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #e53e3e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>KTM Warehouse & Distribution Center</h2>
            <p>Invoice #{{ $invoice->invoice_number }}</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $invoice->client->name }}</strong>,</p>
            
            <p>Thank you for using KTM-WDC services. Please find attached your invoice.</p>
            
            <div class="invoice-details">
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('F d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->payment_due_date->format('F d, Y') }}</p>
                <p><strong>Total Amount:</strong> रू {{ number_format($invoice->grand_total, 2) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->payment_status) }}</p>
            </div>
            
            <p>You can view and pay your invoice by clicking the button below:</p>
            
            <p style="text-align: center;">
                <a href="{{ route('invoices.show', $invoice) }}" class="button">View Invoice</a>
            </p>
            
            <p>If you have any questions, please contact our accounts department.</p>
        </div>
        
        <div class="footer">
            <p>KTM Warehouse & Distribution Center | Kathmandu, Nepal</p>
            <p>Email: accounts@ktm-wdc.com | Phone: +977-1-5551234</p>
        </div>
    </div>
</body>
</html>