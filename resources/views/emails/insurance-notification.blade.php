<!DOCTYPE html>
<html>
<head><title>Insurance Documents</title></head>
<body>
    <h2>New Warehouse Request – Insurance Required</h2>
    <p><strong>Client:</strong> {{ $request->client->name }} ({{ $request->client->email }})</p>
    <p><strong>Required Space:</strong> {{ $request->required_space }} m³</p>
    <p><strong>Duration:</strong> {{ $request->duration_months }} months</p>
    <p><strong>Start Date:</strong> {{ now()->format('d M Y') }}</p>
    <p><strong>End Date:</strong> {{ now()->addMonths($request->duration_months)->format('d M Y') }}</p>
    <p>Attached are the invoice and packing list. Please provide insurance coverage for this period.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>