<!DOCTYPE html>
<html>
<head><title>Contract Expiry Reminder</title></head>
<body>
    <h2>Warehouse Contract Expiry Reminder</h2>
    <p>Dear {{ $request->client->name }},</p>
    <p>Your warehouse contract expires in <strong>{{ $daysLeft }} days</strong> (on {{ \Carbon\Carbon::parse($request->contract_end_date)->format('d M Y') }}).</p>
    <p>Please contact us to renew or make arrangements to remove your goods.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>