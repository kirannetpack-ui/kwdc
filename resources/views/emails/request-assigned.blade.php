<!DOCTYPE html>
<html>
<head><title>Request Assigned</title></head>
<body>
    <h2>Dear {{ $request->client->name }},</h2>
    <p>Your warehouse request #{{ $request->id }} has been assigned to:</p>
    <p><strong>{{ $request->assignedWarehouse->name ?? 'N/A' }}</strong><br>
    Location: {{ $request->assignedWarehouse->address ?? 'Main warehouse' }}</p>
    <p>You can now manage your stock and create dispatch orders.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>