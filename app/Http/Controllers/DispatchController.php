<?php

namespace App\Http\Controllers;

use App\Models\DispatchOrder;
use App\Models\DeliveryStop;
use App\Models\DriverRate;
use App\Models\PickupRequest;
use App\Models\Vehicle;
use App\Events\LocationUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $query = DispatchOrder::with(['client', 'driver', 'vehicle']);

        if ($user->role === 'client') {
            $query->where('client_id', $user->id);
        } elseif ($user->role === 'driver') {
            $query->where('driver_id', $user->id);
        }
        // admin sees all

        $dispatches = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('dispatch.index', compact('dispatches'));
    }

    public function create($requestId = null)
    {
        // Handle creation logic (not the focus)
        return view('dispatch.create');
    }

    public function store(Request $request)
    {
        // Simplified store for testing – adapt to your actual logic
        $validator = Validator::make($request->all(), [
            'pickup_address' => 'required|string',
            'delivery_address' => 'nullable|string',
            'total_distance' => 'nullable|numeric',
            'base_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dispatch = DispatchOrder::create([
            'client_id' => auth()->id(),
            'pickup_address' => $request->pickup_address,
            'total_distance' => $request->total_distance ?? 0,
            'base_price' => $request->base_price ?? 0,
            'status' => 'pending',
            'tracking_id' => 'TRK-' . strtoupper(uniqid()),
        ]);

        return redirect()->route('dispatch.index')->with('success', 'Dispatch created.');
    }

    public function show($id)
    {
        $dispatch = DispatchOrder::with(['client', 'driver', 'deliveryStops'])->findOrFail($id);
        return view('dispatch.show', compact('dispatch'));
    }

    public function updateLocation(Request $request, $id)
    {
        $dispatch = DispatchOrder::findOrFail($id);

        // Authorization: only the assigned driver or admin can update
        if (auth()->user()->role !== 'admin' && auth()->id() !== $dispatch->driver_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $dispatch->current_latitude = $request->latitude;
        $dispatch->current_longitude = $request->longitude;
        $dispatch->last_location_update = now();
        $dispatch->save();

        // --- ETA Calculation (if delivery coordinates exist) ---
      // --- ETA Calculation using Google Traffic ---
$eta = null;
if ($dispatch->delivery_lat && $dispatch->delivery_lng) {
    $googleService = new \App\Services\GoogleMapsService();
    $durationSeconds = $googleService->getTrafficDuration(
        $request->latitude,
        $request->longitude,
        $dispatch->delivery_lat,
        $dispatch->delivery_lng
    );

    if ($durationSeconds) {
        $eta = now()->addSeconds($durationSeconds);
        $dispatch->estimated_arrival = $eta;
        $dispatch->save();
    } else {
        // Fallback to Haversine if API fails
        $distance = $this->haversineDistance(...);
        $avgSpeed = 30;
        $timeInHours = $distance / $avgSpeed;
        $eta = now()->addHours($timeInHours);
        $dispatch->estimated_arrival = $eta;
        $dispatch->save();
    }
}
        // --- End ETA ---

        broadcast(new LocationUpdated($dispatch->id, $request->latitude, $request->longitude, $eta));

        return response()->json([
            'success' => true,
            'eta' => $eta ? $eta->toIso8601String() : null,
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // ------------------------------------------------------------
    // Additional methods (your existing ones – keep them)
    // For test purposes, we include a minimal updateStatus method.
    // ------------------------------------------------------------

    public function updateStatus(Request $request, $id)
{
    $dispatch = DispatchOrder::findOrFail($id);
    $status = $request->status;

    if (!in_array($status, ['pending', 'in_progress', 'delivered', 'cancelled'])) {
        return response()->json(['error' => 'Invalid status'], 400);
    }

    $dispatch->status = $status;

    if ($status === 'delivered') {
        $dispatch->delivered_at = now();

        // --- Create Partner Earning ---
        if ($dispatch->driver_id && $dispatch->base_price > 0) {
            $driverEarning = $dispatch->base_price * 0.75; // 75% to driver

            \App\Models\PartnerEarning::create([
                'partner_id' => $dispatch->driver_id,   // key change
                'order_type' => 'dispatch',              // required
                'order_id' => $dispatch->id,             // required
                'amount' => $driverEarning,
                'status' => 'pending',
                'earned_at' => now(),
            ]);
        }
    }

    $dispatch->save();

    return response()->json(['success' => true]);
}



    // Other methods (directCreate, track, enableTracking, etc.)
    // Keep your existing code below…
}