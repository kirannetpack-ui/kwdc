<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>KTM-WDC Document</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .page {
            padding: 40px 50px;
            position: relative;
        }
        .header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 22px;
            color: #1e293b;
            margin: 0;
            font-weight: bold;
        }
        .header .brand {
            font-size: 14px;
            color: #f59e0b;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            text-align: left;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-gray { color: #6b7280; }
        .mt-20 { margin-top: 20px; }
        .mb-10 { margin-bottom: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>KTM-WDC</h1>
                <div class="brand">Warehouse & Distribution Center</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: bold;">@yield('doc_title', 'OFFICIAL DOCUMENT')</div>
                <div style="font-size: 11px; color: #6b7280;">Issued: {{ now()->format('F j, Y') }}</div>
                <div style="font-size: 11px; color: #6b7280;">Reference: @yield('doc_ref', 'N/A')</div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div>
            @yield('content')
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>This is an auto-generated document from KTM-WDC. All information contained herein is confidential.</p>
            <p style="margin-top: 5px;">© {{ date('Y') }} KTM-WDC. All rights reserved.</p>
        </div>
    </div>
</body>
</html>