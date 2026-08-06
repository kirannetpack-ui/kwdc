<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'driver_code',
        'vehicle_number',
        'vehicle_type',
        'custom_vehicle_type',
        'capacity',
        'capacity_unit',
        'manufacturer',
        'model',
        'year',
        'color',
        'fuel_type',
        'registration_number',
        'registration_date',
        'insurance_number',
        'insurance_valid_until',
        'fitness_certificate_number',
        'fitness_valid_until',
        'pollution_certificate_number',
        'pollution_valid_until',
        'permit_number',
        'permit_valid_until',
        'blue_book_number',
        'insurance_file_path',
        'fitness_file_path',
        'pollution_file_path',
        'permit_file_path',
        'blue_book_file_path',
        'front_photo_path',
        'back_photo_path',
        'left_photo_path',
        'right_photo_path',
        'interior_photo_path',
        'status',
        'is_verified',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'description',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'insurance_valid_until' => 'date',
        'fitness_valid_until' => 'date',
        'pollution_valid_until' => 'date',
        'permit_valid_until' => 'date',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'capacity' => 'decimal:2',
    ];

    // Relationship to the owner (user_id)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the driver (driver_id)
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Generate unique vehicle registration number
    public static function generateVehicleCode()
    {
        $year = now()->year;
        $lastVehicle = self::where('registration_number', 'like', "VEH-{$year}-%")
            ->orderBy('registration_number', 'desc')
            ->first();

        if ($lastVehicle) {
            $lastNumber = intval(substr($lastVehicle->registration_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "VEH-{$year}-{$newNumber}";
    }
}