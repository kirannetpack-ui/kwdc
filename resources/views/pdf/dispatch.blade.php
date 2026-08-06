@extends('pdf.layout')

@section('doc_title', 'DISPATCH ORDER')
@section('doc_ref', $dispatch->tracking_id ?? $dispatch->id)

@section('content')
    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
        <div>
            <h2 style="margin: 0; color: #1e293b;">Dispatch #{{ $dispatch->id }}</h2>
            <p style="margin: 0; color: #6b7280; font-size: 13px;">Tracking: <strong>{{ $dispatch->tracking_id }}</strong></p>
        </div>
        <div style="text-align: right; border-left: 1px solid #e2e8f0; padding-left: 15px;">
            <div style="font-size: 11px; color: #6b7280;">Status</div>
            <div style="font-weight: bold; color: #22c55e;">{{ ucfirst($dispatch->status) }}</div>
        </div>
    </div>

    <table>
        <tr>
            <th style="width: 20%;">Client</th>
            <td>{{ $dispatch->client->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Driver</th>
            <td>{{ $dispatch->driver->name ?? 'Unassigned' }}</td>
        </tr>
        <tr>
            <th>Vehicle Type</th>
            <td>{{ $dispatch->driver->vehicle_type ?? 'Standard' }}</td>
        </tr>
        <tr>
            <th>Distance</th>
            <td>{{ $dispatch->total_distance }} km</td>
        </tr>
    </table>

    <div class="mt-20">
        <div class="font-bold mb-10">Stops</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th>Address</th>
                    <th>Recipient</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dispatch->stops as $index => $stop)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $stop->address }}</td>
                    <td>{{ $stop->recipient_name }}</td>
                    <td>{{ $stop->recipient_phone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-20 text-right" style="border-top: 2px solid #f59e0b; padding-top: 10px;">
        <div style="font-size: 14px; font-weight: bold;">
            Total Amount: <span style="color: #f59e0b;">रू {{ number_format($dispatch->grand_total ?? 0, 2) }}</span>
        </div>
    </div>
@endsection
