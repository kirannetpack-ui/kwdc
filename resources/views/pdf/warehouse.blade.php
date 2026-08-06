@extends('pdf.layout')

@section('doc_title', 'WAREHOUSE REGISTRATION CERTIFICATE')
@section('doc_ref', $warehouse->id)

@section('content')
    <div style="margin-bottom: 15px;">
        <h2 style="margin: 0 0 5px 0; color: #1e293b;">{{ $warehouse->name }}</h2>
        <p style="margin: 0; color: #6b7280;">{{ $warehouse->address }}</p>
    </div>

    <table>
        <tr>
            <th style="width: 30%;">Owner Name</th>
            <td>{{ $warehouse->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Contact Number</th>
            <td>{{ $warehouse->contact_number }}</td>
        </tr>
        <tr>
            <th>Total Area</th>
            <td>{{ $warehouse->area_sqft }} sq ft</td>
        </tr>
        <tr>
            <th>Price Per Sq Ft</th>
            <td>रू {{ number_format($warehouse->price_per_sqft, 2) }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <span style="background: @if($warehouse->status == 'approved') #22c55e @elseif($warehouse->status == 'pending') #f59e0b @else #ef4444 @endif; color: white; padding: 2px 10px; border-radius: 4px; font-size: 11px;">
                    {{ ucfirst($warehouse->status) }}
                </span>
            </td>
        </tr>
        <tr>
    <th>Facilities</th>
    <td>
        @if(is_array($warehouse->facilities) && count($warehouse->facilities) > 0)
            @foreach($warehouse->facilities as $facility)
                <span style="background: #f3f4f6; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-right: 4px; display: inline-block;">{{ $facility }}</span>
            @endforeach
        @else
            <span class="text-gray-400">None specified</span>
        @endif
    </td>
</tr>
    </table>

    @if($warehouse->description)
        <div class="mt-20">
            <div class="font-bold mb-10">Description</div>
            <p style="color: #4b5563;">{{ $warehouse->description }}</p>
        </div>
    @endif
@endsection