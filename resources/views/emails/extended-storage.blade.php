<!DOCTYPE html>
<html>
<head><title>Extended Storage Reminder</title></head>
<body>
    <h2>Extended Storage Alert</h2>
    <p>Dear {{ $request->client->name }},</p>
    <p>Your goods have been in storage for <strong>{{ $daysOverdue }} days</strong> beyond the contract end date.</p>
    <p>Please arrange to remove them immediately. If not removed within 60 days, they will be auctioned.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>