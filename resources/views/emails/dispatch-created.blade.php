<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Created</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .tracking-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🚚 KTM-WDC Logistics</div>
            <div>Professional Delivery Network</div>
        </div>
        
        <div class="content">
            <h2>Dear {{ $recipient_name }},</h2>
            
            <p>Your dispatch order has been <strong>successfully created</strong> and is now being processed.</p>
            
            <div class="tracking-box">
                <strong>📦 Dispatch Details:</strong><br>
                Dispatch ID: <strong>#{{ $dispatch->invoice_no }}</strong><br>
                Status: <span class="status-badge">{{ ucfirst($dispatch->status) }}</span><br>
                Total Distance: <strong>{{ number_format($dispatch->total_distance, 2) }} km</strong><br>
                Total Cost: <strong>रू {{ number_format($dispatch->total_price, 2) }}</strong><br>
                Created: {{ $dispatch->created_at->format('F d, Y H:i') }}
            </div>
            
            <p><strong>📍 Delivery Stops:</strong></p>
            <table>
                <tr><th>Stop</th><th>Location</th><th>Recipient</th></tr>
                @foreach($dispatch->stops as $index => $stop)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $stop->address }}</td>
                    <td>{{ $stop->recipient_name }}</td>
                </tr>
                @endforeach
            </table>
            
            <div style="text-align: center;">
                <a href="{{ $tracking_url }}" class="button">🔍 Track Your Dispatch</a>
            </div>
            
            <p><strong>📎 Attached:</strong> Dispatch Summary (PDF)</p>
            
            <p><strong>Need help?</strong><br>
            Call our support: <strong>{{ $support_phone }}</strong><br>
            Email: <strong>support@ktmwdc.com</strong></p>
        </div>
        
        <div class="footer">
            <p>KTM Warehouse & Distribution Center<br>
            Kathmandu, Nepal | PAN: 123456789<br>
            &copy; {{ date('Y') }} KTM-WDC. All rights reserved.</p>
            <p style="font-size: 11px;">This is an automated message. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>