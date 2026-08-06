@extends('emails.layout')

@section('content')
<div class="animate-fade">
    <div style="margin-bottom: 25px;">
        <h2 style="color: #1f2937; margin: 0 0 10px;">🙏 Namaste {{ $recipientName }}!</h2>
        <p style="color: #4b5563; font-size: 16px; margin: 0;">Congratulations! Your warehouse has been approved.</p>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <div class="animate-pulse" style="display: inline-block; background: #d1fae5; border-radius: 50%; padding: 20px;">
            <span style="font-size: 50px;">✅</span>
        </div>
    </div>
    
    <div style="background: #f0fdf4; border-radius: 12px; padding: 20px; margin: 20px 0;">
        <h3 style="color: #1f2937; margin: 0 0 15px;">Warehouse Details</h3>
        <p><strong>Name:</strong> {{ $warehouseName }}</p>
        <p><strong>Location:</strong> {{ $warehouseLocation }}</p>
        <p><strong>Status:</strong> <span style="color: #059669;">Approved</span></p>
    </div>
    
    <div style="text-align: center; margin-top: 25px;">
        <a href="{{ $dashboardUrl }}" style="display: inline-block; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 600;">View Your Dashboard →</a>
    </div>
</div>
@endsection