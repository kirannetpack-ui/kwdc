<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dispatch;
use App\Models\PickupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $driverId = auth()->id();
        
        // My Jobs Count (Total assigned jobs)
        $myJobsCount = Dispatch::where('driver_id', $driverId)
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way', 'in_progress'])
            ->count();
        
        // Completed Jobs Count
        $completedJobs = Dispatch::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->count();
        
        // Available Jobs Count (Jobs without driver assigned)
        $availableJobs = Dispatch::whereNull('driver_id')
            ->where('status', 'pending')
            ->count();
        
        // Total Earnings
        $totalEarnings = Dispatch::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->sum('driver_earning');
        
        // Active Jobs List (for the left panel)
        $activeJobs = Dispatch::where('driver_id', $driverId)
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Available Jobs List (for the right panel)
        $availableJobsList = Dispatch::whereNull('driver_id')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Recent Completed Jobs
        $recentCompleted = Dispatch::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('driver.dashboard', compact(
            'myJobsCount',
            'completedJobs',
            'availableJobs',
            'totalEarnings',
            'activeJobs',
            'availableJobsList',
            'recentCompleted'
        ));
    }

    public function jobs()
    {
        $activeJobs = Dispatch::where('driver_id', auth()->id())
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $completedJobs = Dispatch::where('driver_id', auth()->id())
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('driver.jobs', compact('activeJobs', 'completedJobs'));
    }

    public function availableJobs()
    {
        $jobs = Dispatch::whereNull('driver_id')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('driver.available_jobs', compact('jobs'));
    }

    public function accept($orderId)
    {
        $dispatch = Dispatch::findOrFail($orderId);
        $dispatch->driver_id = auth()->id();
        $dispatch->driver_code = auth()->user()->user_code;
        $dispatch->status = 'assigned';
        $dispatch->assigned_at = now();
        $dispatch->save();
        
        return redirect()->back()->with('success', 'Job accepted successfully!');
    }

    public function deliver($orderId, Request $request)
    {
        $dispatch = Dispatch::findOrFail($orderId);
        $dispatch->status = 'delivered';
        $dispatch->delivered_at = now();
        
        // Calculate driver earning if not set
        if (!$dispatch->driver_earning && $dispatch->price) {
            // Assuming 80% goes to driver, 20% to admin
            $dispatch->driver_earning = $dispatch->price * 0.8;
            $dispatch->admin_commission = $dispatch->price * 0.2;
        }
        
        $dispatch->save();
        
        return redirect()->back()->with('success', 'Delivery completed successfully!');
    }

    public function uploadProof($orderId, Request $request)
    {
        $request->validate([
            'proof_image' => 'required|image|max:2048'
        ]);
        
        $dispatch = Dispatch::findOrFail($orderId);
        $path = $request->file('proof_image')->store('delivery_proofs', 'public');
        $dispatch->delivery_proof = $path;
        $dispatch->save();
        
        return redirect()->back()->with('success', 'Proof uploaded successfully!');
    }

    public function pickupJobs()
    {
        $pickups = PickupRequest::where('driver_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('driver.pickups', compact('pickups'));
    }

    public function completePickup($id)
    {
        $pickup = PickupRequest::findOrFail($id);
        $pickup->status = 'completed';
        $pickup->completed_at = now();
        $pickup->save();
        
        return redirect()->back()->with('success', 'Pickup completed successfully!');
    }

    public function proposePrice($id, Request $request)
    {
        $request->validate([
            'proposed_price' => 'required|numeric|min:0'
        ]);
        
        $dispatch = Dispatch::findOrFail($id);
        $dispatch->proposed_price = $request->proposed_price;
        $dispatch->proposal_status = 'pending';
        $dispatch->save();
        
        return redirect()->back()->with('success', 'Price proposed successfully!');
    }

    public function earnings()
    {
        $totalEarnings = Dispatch::where('driver_id', auth()->id())
            ->where('status', 'delivered')
            ->sum('driver_earning');
        
        $completedJobsCount = Dispatch::where('driver_id', auth()->id())
            ->where('status', 'delivered')
            ->count();
        
        $averageEarning = $completedJobsCount > 0 ? $totalEarnings / $completedJobsCount : 0;
        
        $transactions = Dispatch::where('driver_id', auth()->id())
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->paginate(20);
        
        return view('driver.earnings', compact('totalEarnings', 'completedJobsCount', 'averageEarning', 'transactions'));
    }
    
    public function rates()
    {
        // Get driver rates
        $rates = \App\Models\DriverRate::where('driver_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $currentRate = \App\Models\DriverRate::where('driver_id', auth()->id())
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>', now());
            })
            ->first();
        
        return view('driver.rates', compact('rates', 'currentRate'));
    }

const dispatchId = {{ $dispatch->id }};

function sendLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                fetch('/dispatch/' + dispatchId + '/update-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Location update failed');
                    }
                })
                .catch(err => console.error(err));
            },
            function(error) {
                console.warn('Geolocation error:', error.message);
            }
        );
    } else {
        console.warn('Geolocation not supported');
    }
}

// Send location every 10 seconds
setInterval(sendLocation, 10000);

}