<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
   

    public function create()
    {
        return view('equipment.register');
    }

  public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'location' => 'required|string',
        'daily_rate' => 'nullable|numeric|min:0',
    ]);

    // Get the actual column names from your table
    $data = [
        'owner_id' => auth()->id(),
        'name' => $request->name,
        'type' => $request->type,
        'model' => $request->model,
        'year' => $request->year,
        'description' => $request->description,
        'weight' => $request->weight,
        'engine_power' => $request->engine_power,
        'bucket_capacity' => $request->bucket_capacity,
        'max_reach' => $request->max_reach,
        'daily_rate' => $request->daily_rate,
        'weekly_rate' => $request->weekly_rate,
        'monthly_rate' => $request->monthly_rate,
        'security_deposit' => $request->security_deposit,
        'location' => $request->location,
        'status' => $request->status ?? 'available',
    ];

    // Handle photo uploads
    if ($request->hasFile('front_photo')) {
        $data['front_photo'] = $request->file('front_photo')->store('equipment/photos', 'public');
    }
    if ($request->hasFile('side_photo')) {
        $data['side_photo'] = $request->file('side_photo')->store('equipment/photos', 'public');
    }
    if ($request->hasFile('working_photo')) {
        $data['working_photo'] = $request->file('working_photo')->store('equipment/photos', 'public');
    }

    // Handle document uploads
    if ($request->hasFile('registration_doc')) {
        $data['registration_doc'] = $request->file('registration_doc')->store('equipment/documents', 'private_uploads');
    }
    if ($request->hasFile('insurance_doc')) {
        $data['insurance_doc'] = $request->file('insurance_doc')->store('equipment/documents', 'private_uploads');
    }

    // Remove any null values
    $data = array_filter($data, function($value) {
        return !is_null($value);
    });

    try {
        $equipment = Equipment::create($data);
        return redirect()->route('equipment.list')
            ->with('success', 'Equipment registered successfully.');
    } catch (\Exception $e) {
        \Log::error('Equipment creation error: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Failed to save: ' . $e->getMessage()])->withInput();
    }
}

    public function list()
    {
        $equipment = Equipment::where('owner_id', auth()->id())->latest()->get();
        return view('equipment.list', compact('equipment'));
    }

    public function dashboard()
    {
        $myEquipment = Equipment::where('owner_id', auth()->id())->count();
        $activeJobs = \App\Models\EquipmentJob::where('owner_id', auth()->id())
            ->where('status', 'assigned')->count();
        $completedJobs = \App\Models\EquipmentJob::where('owner_id', auth()->id())
            ->where('status', 'completed')->count();
        $totalEarnings = \App\Models\EquipmentJob::where('owner_id', auth()->id())
            ->where('status', 'completed')->sum('price') ?? 0;
        
        $equipmentList = Equipment::where('owner_id', auth()->id())->latest()->get();
        
        return view('equipment.dashboard', compact('myEquipment', 'activeJobs', 'completedJobs', 'totalEarnings', 'equipmentList'));
    }

    public function destroy($id)
    {
        $equipment = Equipment::where('owner_id', auth()->id())->findOrFail($id);
        $equipment->delete();
        
        return redirect()->route('equipment.list')
            ->with('success', 'Equipment deleted successfully.');
    }

public function edit($id)
{
    $equipment = Equipment::where('owner_id', auth()->id())->findOrFail($id);
    return view('equipment.edit', compact('equipment'));
}

public function update(Request $request, $id)
{
    $equipment = Equipment::where('owner_id', auth()->id())->findOrFail($id);
    
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'location' => 'required|string',
        'daily_rate' => 'nullable|numeric|min:0',
    ]);

    $data = [
        'name' => $request->name,
        'type' => $request->type,
        'model' => $request->model,
        'year' => $request->year,
        'description' => $request->description,
        'weight' => $request->weight,
        'engine_power' => $request->engine_power,
        'bucket_capacity' => $request->bucket_capacity,
        'max_reach' => $request->max_reach,
        'daily_rate' => $request->daily_rate,
        'weekly_rate' => $request->weekly_rate,
        'monthly_rate' => $request->monthly_rate,
        'security_deposit' => $request->security_deposit,
        'location' => $request->location,
        'status' => $request->status ?? 'available',
    ];

    // Handle photo uploads
    if ($request->hasFile('front_photo')) {
        $data['front_photo'] = $request->file('front_photo')->store('equipment/photos', 'public');
    }
    if ($request->hasFile('side_photo')) {
        $data['side_photo'] = $request->file('side_photo')->store('equipment/photos', 'public');
    }
    if ($request->hasFile('working_photo')) {
        $data['working_photo'] = $request->file('working_photo')->store('equipment/photos', 'public');
    }

    // Handle document uploads
    if ($request->hasFile('registration_doc')) {
        $data['registration_doc'] = $request->file('registration_doc')->store('equipment/documents', 'private_uploads');
    }
    if ($request->hasFile('insurance_doc')) {
        $data['insurance_doc'] = $request->file('insurance_doc')->store('equipment/documents', 'private_uploads');
    }

    $data = array_filter($data, function($value) {
        return !is_null($value);
    });

    $equipment->update($data);

    return redirect()->route('equipment.list')
        ->with('success', 'Equipment updated successfully.');
}

}
