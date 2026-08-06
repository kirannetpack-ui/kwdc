<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverVehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::where('driver_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('driver.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('driver.vehicles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles',
            'vehicle_type' => 'required|string|max:100',
            'custom_vehicle_type' => 'required_if:vehicle_type,other|nullable|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'capacity_unit' => 'required|string|max:50',
            'manufacturer' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'fuel_type' => 'nullable|string|in:petrol,diesel,electric,cng',
            'registration_date' => 'nullable|date',
            'insurance_number' => 'nullable|string|max:255',
            'insurance_valid_until' => 'nullable|date',
            'fitness_certificate_number' => 'nullable|string|max:255',
            'fitness_valid_until' => 'nullable|date',
            'pollution_certificate_number' => 'nullable|string|max:255',
            'pollution_valid_until' => 'nullable|date',
            'permit_number' => 'nullable|string|max:255',
            'permit_valid_until' => 'nullable|date',
            'blue_book_number' => 'nullable|string|max:255',
        ]);

        // Handle file uploads
        $insuranceFile = $this->uploadFile($request->file('insurance_file'), 'insurance');
        $fitnessFile = $this->uploadFile($request->file('fitness_file'), 'fitness');
        $pollutionFile = $this->uploadFile($request->file('pollution_file'), 'pollution');
        $permitFile = $this->uploadFile($request->file('permit_file'), 'permit');
        $blueBookFile = $this->uploadFile($request->file('blue_book_file'), 'blue_book');
        
        $frontPhoto = $this->uploadFile($request->file('front_photo'), 'photos');
        $backPhoto = $this->uploadFile($request->file('back_photo'), 'photos');
        $leftPhoto = $this->uploadFile($request->file('left_photo'), 'photos');
        $rightPhoto = $this->uploadFile($request->file('right_photo'), 'photos');
        $interiorPhoto = $this->uploadFile($request->file('interior_photo'), 'photos');

        $vehicle = Vehicle::create([
            'driver_id' => auth()->id(),
            'driver_code' => auth()->user()->user_code,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_type' => $request->vehicle_type,
            'custom_vehicle_type' => $request->vehicle_type == 'other' ? $request->custom_vehicle_type : null,
            'capacity' => $request->capacity,
            'capacity_unit' => $request->capacity_unit,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'registration_number' => Vehicle::generateVehicleCode(),
            'registration_date' => $request->registration_date,
            'insurance_number' => $request->insurance_number,
            'insurance_valid_until' => $request->insurance_valid_until,
            'insurance_file_path' => $insuranceFile,
            'fitness_certificate_number' => $request->fitness_certificate_number,
            'fitness_valid_until' => $request->fitness_valid_until,
            'fitness_file_path' => $fitnessFile,
            'pollution_certificate_number' => $request->pollution_certificate_number,
            'pollution_valid_until' => $request->pollution_valid_until,
            'pollution_file_path' => $pollutionFile,
            'permit_number' => $request->permit_number,
            'permit_valid_until' => $request->permit_valid_until,
            'permit_file_path' => $permitFile,
            'blue_book_number' => $request->blue_book_number,
            'blue_book_file_path' => $blueBookFile,
            'front_photo_path' => $frontPhoto,
            'back_photo_path' => $backPhoto,
            'left_photo_path' => $leftPhoto,
            'right_photo_path' => $rightPhoto,
            'interior_photo_path' => $interiorPhoto,
            'description' => $request->description,
            'status' => 'pending',
            'is_verified' => false,
        ]);

        return redirect()->route('driver.vehicles.index')
            ->with('success', 'Vehicle registered successfully! Awaiting admin approval.');
    }

    private function uploadFile($file, $folder)
    {
        if ($file && $file->isValid()) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vehicle_documents/' . $folder, $filename, 'public');
            return $path;
        }
        return null;
    }

    public function edit($id)
    {
        $vehicle = Vehicle::where('driver_id', auth()->id())->findOrFail($id);
        return view('driver.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::where('driver_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,' . $vehicle->id,
            'vehicle_type' => 'required|string|max:100',
            'custom_vehicle_type' => 'required_if:vehicle_type,other|nullable|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'capacity_unit' => 'required|string|max:50',
        ]);

        $vehicle->update([
            'vehicle_number' => $request->vehicle_number,
            'vehicle_type' => $request->vehicle_type,
            'custom_vehicle_type' => $request->vehicle_type == 'other' ? $request->custom_vehicle_type : null,
            'capacity' => $request->capacity,
            'capacity_unit' => $request->capacity_unit,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'description' => $request->description,
        ]);

        return redirect()->route('driver.vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

public function show($id)
{
    $vehicle = Vehicle::where('driver_id', auth()->id())->findOrFail($id);
    return view('driver.vehicles.show', compact('vehicle'));
}
    public function destroy($id)
    {
        $vehicle = Vehicle::where('driver_id', auth()->id())->findOrFail($id);
        
        // Delete associated files
        $files = [
            'insurance_file_path', 'fitness_file_path', 'pollution_file_path',
            'permit_file_path', 'blue_book_file_path', 'front_photo_path',
            'back_photo_path', 'left_photo_path', 'right_photo_path', 'interior_photo_path'
        ];
        
        foreach ($files as $fileField) {
            if ($vehicle->$fileField && Storage::disk('public')->exists($vehicle->$fileField)) {
                Storage::disk('public')->delete($vehicle->$fileField);
            }
        }
        
        $vehicle->delete();
        
        return redirect()->route('driver.vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }
}