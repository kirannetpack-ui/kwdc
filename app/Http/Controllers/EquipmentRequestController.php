<?php

namespace App\Http\Controllers;

use App\Models\EquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\AIService;

class EquipmentRequestController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->middleware('auth');
        $this->aiService = $aiService;
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->role == 'client') {
            $requests = EquipmentRequest::where('client_id', $user->id)
                ->with(['equipment', 'assignedEquipment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } elseif ($user->role == 'equipment_owner') {
            $equipmentIds = Equipment::where('user_id', $user->id)->pluck('id');
            $requests = EquipmentRequest::whereIn('equipment_id', $equipmentIds)
                ->orWhere('equipment_type', 'like', '%')
                ->with(['client', 'equipment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $requests = EquipmentRequest::with(['client', 'equipment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('equipment-requests.index', compact('requests'));
    }

    public function create()
    {
        $equipment = Equipment::where('status', 'available')->get();
        return view('equipment-requests.create', compact('equipment'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipment_type' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:500',
            'description' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'budget_range' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $requestData = $request->all();
        $requestData['client_id'] = Auth::id();
        $requestData['status'] = 'pending';

        if ($request->has('preferred_brands')) {
            $requestData['preferred_brands'] = explode(',', $request->preferred_brands);
        }

        $equipmentRequest = EquipmentRequest::create($requestData);

        return redirect()->route('equipment-requests.show', $equipmentRequest->id)
            ->with('success', 'Equipment request created successfully!');
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $request = EquipmentRequest::with(['client', 'equipment', 'assignedEquipment'])
            ->findOrFail($id);
        
        if ($user->role != 'admin' && 
            $request->client_id != $user->id && 
            $request->equipment->user_id != $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('equipment-requests.show', compact('request'));
    }

    public function edit($id)
    {
        $request = EquipmentRequest::where('client_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $equipment = Equipment::where('status', 'available')->get();
        
        return view('equipment-requests.edit', compact('request', 'equipment'));
    }

    public function update(Request $request, $id)
    {
        $equipmentRequest = EquipmentRequest::where('client_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'equipment_type' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:500',
            'description' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'budget_range' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $requestData = $request->all();
        
        if ($request->has('preferred_brands')) {
            $requestData['preferred_brands'] = explode(',', $request->preferred_brands);
        }

        $equipmentRequest->update($requestData);

        return redirect()->route('equipment-requests.show', $equipmentRequest->id)
            ->with('success', 'Equipment request updated successfully!');
    }

    public function destroy($id)
    {
        $equipmentRequest = EquipmentRequest::where('client_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $equipmentRequest->delete();

        return redirect()->route('equipment-requests.index')
            ->with('success', 'Equipment request deleted successfully!');
    }

    public function approve($id)
    {
        $request = EquipmentRequest::findOrFail($id);
        $equipment = Equipment::where('id', $request->equipment_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$equipment) {
            return redirect()->back()->with('error', 'You do not own this equipment.');
        }
        
        $request->accept();
        return redirect()->back()->with('success', 'Equipment request approved!');
    }

    public function reject($id)
    {
        $request = EquipmentRequest::findOrFail($id);
        $equipment = Equipment::where('id', $request->equipment_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$equipment) {
            return redirect()->back()->with('error', 'You do not own this equipment.');
        }
        
        $request->reject();
        return redirect()->back()->with('success', 'Equipment request rejected.');
    }

    public function fulfill($id)
    {
        $request = EquipmentRequest::findOrFail($id);
        $equipment = Equipment::where('id', $request->equipment_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$equipment) {
            return redirect()->back()->with('error', 'You do not own this equipment.');
        }
        
        $request->complete();
        $equipment->update(['status' => 'rented']);
        return redirect()->back()->with('success', 'Equipment request fulfilled!');
    }

    public function return($id)
    {
        $request = EquipmentRequest::findOrFail($id);
        $equipment = Equipment::where('id', $request->equipment_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$equipment) {
            return redirect()->back()->with('error', 'You do not own this equipment.');
        }
        
        $equipment->update(['status' => 'available']);
        $request->status = 'returned';
        $request->save();
        return redirect()->back()->with('success', 'Equipment returned successfully!');
    }

    public function recommendEquipment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipment_type' => 'required|string',
            'duration' => 'required|integer|min:1',
            'location' => 'required|string',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'commodity_name' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'loading_site' => 'nullable|string|max:500',
            'unloading_site' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $result = $this->aiService->recommendEquipmentForJob(
            $request->equipment_type,
            $request->duration,
            $request->location,
            $request->description,
            $request->budget,
            $request->commodity_name,
            $request->weight,
            $request->dimensions,
            $request->loading_site,
            $request->unloading_site
        );

        return response()->json([
            'success' => true,
            'recommendation' => $result
        ]);
    }
}