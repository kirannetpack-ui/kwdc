<!DOCTYPE html>
<html>
<head><title>Warehouse Update</title></head>
<body>
    <h2>Dear {{ $warehouse->owner->name }},</h2>
    <p>Your warehouse <strong>{{ $warehouse->name }}</strong> could not be approved at this time.</p>
    <p>Please contact KTM-WDC for more information.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>