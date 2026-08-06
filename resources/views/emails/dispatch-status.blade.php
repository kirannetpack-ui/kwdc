<!DOCTYPE html>
<html>
<head><title>Dispatch Order Update</title></head>
<body>
    <h2>Dear {{ $order->warehouseRequest->client->name }},</h2>
    <p>Your dispatch order #{{ $order->id }} status has changed to:</p>
    <p><strong>{{ ucfirst($order->status) }}</strong></p>
    @if($order->status == 'assigned')
        <p>Vehicle assigned: {{ $order->vehicle->registration_number ?? 'N/A' }} (Driver: {{ $order->vehicle->driver_name ?? 'N/A' }})</p>
    @elseif($order->status == 'delivered')
        <p>Order has been delivered. Proof of delivery is available in your dashboard.</p>
    @endif
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>