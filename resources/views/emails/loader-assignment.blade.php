<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loader Assignment</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; }
        .header { border-bottom: 3px solid #3b82f6; padding-bottom: 20px; }
        .content { line-height: 1.6; }
        .info { background: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1 style="color:#3b82f6;">📋 New Loader Assignment</h1></div>
        <div class="content">
            <p>You have been assigned a new loading/unloading job.</p>
            <div class="info">
                <p><strong>Dispatch ID:</strong> #{{ $assignment->dispatch_id }}</p>
                <p><strong>Date:</strong> {{ $assignment->assignment_date->format('F j, Y') }}</p>
                <p><strong>Start Time:</strong> {{ $assignment->start_time->format('g:i A') }}</p>
                <p><strong>Required Loaders:</strong> {{ $assignment->required_loaders }}</p>
                <p><strong>Status:</strong> {{ ucfirst($assignment->status) }}</p>
                <p><strong>Notes:</strong> {{ $assignment->notes ?? 'N/A' }}</p>
            </div>
            <p>Please log in to coordinate and assign loaders.</p>
        </div>
    </div>
</body>
</html>