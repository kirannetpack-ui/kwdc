<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NepaliDemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(env('KWDC_DEMO_PASSWORD', 'local-demo-password'));

        $admin = $this->user('admin.demo@kwdc.test', 'Demo Admin', 'admin', $password, [
            'is_admin' => true,
            'phone' => '9800000000',
            'address' => 'Putalisadak, Kathmandu',
            'user_code' => 'ADM-DEMO',
        ]);

        $client = $this->user('client.demo@kwdc.test', 'Sita Enterprises Demo', 'client', $password, [
            'is_client' => true,
            'phone' => '9801112233',
            'address' => 'New Road, Kathmandu',
            'user_code' => 'CLI-DEMO',
        ]);

        $driver = $this->user('driver.demo@kwdc.test', 'Ram Bahadur Driver', 'driver', $password, [
            'is_driver' => true,
            'phone' => '9802223344',
            'address' => 'Koteshwor, Kathmandu',
            'user_code' => 'DRV-DEMO',
        ]);

        $driverTwo = $this->user('driver.laxman@kwdc.test', 'Laxman Gurung', 'driver', $password, [
            'is_driver' => true,
            'phone' => '9802223355',
            'address' => 'Bharatpur, Chitwan',
            'user_code' => 'DRV-LAXMAN',
        ]);

        $propertyOwner = $this->user('property.demo@kwdc.test', 'Maya Warehouse Owner', 'property_owner', $password, [
            'is_property_owner' => true,
            'phone' => '9803334455',
            'address' => 'Satdobato, Lalitpur',
            'user_code' => 'PRO-DEMO',
        ]);

        $equipmentOwner = $this->user('equipment.demo@kwdc.test', 'Krishna Equipment Owner', 'equipment_owner', $password, [
            'is_equipment_owner' => true,
            'phone' => '9804445566',
            'address' => 'Biratnagar, Morang',
            'user_code' => 'EQP-DEMO',
        ]);

        $securityUser = $this->user('security.demo@kwdc.test', 'Nepal Suraksha Agency', 'security_agency', $password, [
            'phone' => '9805556677',
            'address' => 'Baluwatar, Kathmandu',
            'user_code' => 'SEC-DEMO',
        ]);

        $warehouseOne = $this->warehouse($propertyOwner->id, 'Kalimati Cold Store Demo', 'Kalimati, Kathmandu', [
            'address' => 'Kalimati Tarkari Bazaar gate pachadi, Kathmandu',
            'email' => 'kalimati.cold@kwdc.test',
            'area_sqft' => 6200,
            'price_per_sqft' => 52,
            'description' => 'Cold storage for tarkari, dairy, and festival stock with 24/7 guard service.',
            'cold_storage' => true,
            'temperature_min' => -4,
            'temperature_max' => 6,
            'cctv_count' => 18,
            'guards_count' => 4,
            'status' => 'approved',
            'city' => 'Kathmandu',
        ]);

        $warehouseTwo = $this->warehouse($propertyOwner->id, 'Birgunj Transit Godown Demo', 'Birgunj, Parsa', [
            'address' => 'Dry Port road, Birgunj',
            'email' => 'birgunj.transit@kwdc.test',
            'area_sqft' => 14000,
            'price_per_sqft' => 38,
            'description' => 'Large border transit warehouse for FMCG, cement, and import cargo.',
            'loading_dock' => true,
            'parking_spaces' => 22,
            'cctv_count' => 24,
            'guards_count' => 6,
            'status' => 'approved',
            'city' => 'Birgunj',
        ]);

        $this->vehicle($driver->id, 'BA 5 KHA 2244', 'mini truck', 'Tata', 'Ace Gold', 1.5);
        $this->vehicle($driverTwo->id, 'NA 8 KHA 7711', 'truck', 'Eicher', 'Pro 2059', 5.0);

        $this->driverRate($driver->id, 250, 'Mini Truck');
        $this->driverRate($driverTwo->id, 420, 'Heavy Truck');

        $this->dispatch($client->id, $driver->id, 'TRK-KTM-001', 'Kalimati, Kathmandu', 'Bhaktapur Durbar Square', 17.5, 3200, 'assigned');
        $this->dispatch($client->id, $driver->id, 'TRK-PKR-002', 'Nayabazar, Pokhara', 'Lakeside, Pokhara', 8.2, 1800, 'picked_up');
        $this->dispatch($client->id, $driver->id, 'TRK-LTP-003', 'Lagankhel, Lalitpur', 'Boudha, Kathmandu', 13.1, 2500, 'delivered');
        $this->dispatch($client->id, null, 'TRK-BGW-004', 'Birgunj Dry Port', 'Hetauda Industrial Area', 92.0, 14500, 'pending');
        $this->dispatch($client->id, null, 'TRK-ITR-005', 'Itahari Chowk', 'Dharan Bazar', 21.4, 4100, 'pending');

        $this->pickup($client->id, $driver->id, 'PUP-KTM-001', 'Asan, Kathmandu', 9.4, 2100, 'assigned');
        $this->pickup($client->id, $driver->id, 'PUP-LTP-002', 'Jawalakhel, Lalitpur', 6.7, 1600, 'completed');
        $this->pickup($client->id, null, 'PUP-BKT-003', 'Suryabinayak, Bhaktapur', 12.0, 2300, 'pending');

        $stockId = $this->stock($client->id, $warehouseOne->id, 'Ilam Tea Cartons', 'ILAM-TEA-2083', 'SKU-TEA-001', 48);
        $this->box($stockId, $client->id, 'TEA-BOX-001');
        $this->box($stockId, $client->id, 'TEA-BOX-002');

        $this->equipment($equipmentOwner->id, 'JCB Backhoe Loader Demo', 'backhoe_loader', 'Kathmandu Ring Road', 18000);
        $this->equipment($equipmentOwner->id, 'Forklift 3 Ton Demo', 'forklift', 'Birgunj Dry Port', 8500);

        $agencyId = $this->securityAgency($securityUser->id);
        $guardId = $this->securityPersonnel($agencyId, 'Bikash Thapa', 'SEC-GRD-001');
        $this->securityGood($agencyId, 'Handheld Metal Detector', 'screening');
        $this->securityAssignment($warehouseOne->id, $agencyId, $guardId);

        foreach ([$admin, $client, $driver, $propertyOwner, $equipmentOwner, $securityUser] as $user) {
            $this->reminder($user->id, 'Follow up demo calendar task', 'Check today dashboard and pending actions.');
            $this->notification($user->id, 'Demo data ready', 'Nepali demo records have been loaded for this role.');
        }
    }

    private function user(string $email, string $name, string $role, string $password, array $extra): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            array_merge([
                'name' => $name,
                'password' => $password,
                'role' => $role,
                'is_active' => true,
                'email_verified_at' => now(),
            ], $extra)
        );
    }

    private function warehouse(int $userId, string $name, string $location, array $extra): object
    {
        DB::table('warehouses')->updateOrInsert(
            ['name' => $name],
            array_merge([
                'user_id' => $userId,
                'location' => $location,
                'contact_number' => '9807778899',
                'country' => 'Nepal',
                'facilities' => json_encode(['CCTV', 'Loading dock', 'Guard post', 'Parking']),
                'available_from' => now()->addDays(3)->toDateString(),
                'minimum_rental_period' => 1,
                'insurance_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $extra)
        );

        return DB::table('warehouses')->where('name', $name)->first();
    }

    private function vehicle(int $driverId, string $number, string $type, string $manufacturer, string $model, float $capacity): void
    {
        DB::table('vehicles')->updateOrInsert(
            ['vehicle_number' => $number],
            [
                'user_id' => $driverId,
                'driver_id' => $driverId,
                'driver_code' => 'DRV-' . $driverId,
                'vehicle_type' => $type,
                'capacity' => $capacity,
                'capacity_unit' => 'tons',
                'manufacturer' => $manufacturer,
                'model' => $model,
                'year' => 2022,
                'color' => 'White',
                'fuel_type' => 'diesel',
                'status' => 'active',
                'is_verified' => true,
                'verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function driverRate(int $driverId, int $basePrice, string $vehicleType): void
    {
        DB::table('driver_rates')->updateOrInsert(
            ['driver_id' => $driverId, 'date' => now()->toDateString()],
            [
                'user_id' => $driverId,
                'vehicle_type' => $vehicleType,
                'base_price' => $basePrice,
                'rate_tiers' => json_encode(['0_5km' => $basePrice, '6_15km' => $basePrice * 1.8, '16_plus_per_km' => 65]),
                'status' => 'active',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function dispatch(?int $clientId, ?int $driverId, string $tracking, string $pickup, string $delivery, float $distance, int $price, string $status): void
    {
        DB::table('dispatch_orders')->updateOrInsert(
            ['tracking_id' => $tracking],
            [
                'client_id' => $clientId,
                'driver_id' => $driverId,
                'pickup_address' => $pickup,
                'delivery_address' => $delivery,
                'total_distance' => $distance,
                'base_price' => $price,
                'driver_earning' => $driverId ? $price * 0.75 : null,
                'admin_margin' => $price * 0.25,
                'status' => $status,
                'payment_status' => $status === 'delivered' ? 'paid' : 'pending',
                'assigned_at' => $driverId ? now()->subDays(2) : null,
                'picked_up_at' => in_array($status, ['picked_up', 'delivered'], true) ? now()->subDay() : null,
                'delivered_at' => $status === 'delivered' ? now()->subHours(5) : null,
                'created_at' => now()->subDays(rand(1, 9)),
                'updated_at' => now(),
            ]
        );
    }

    private function pickup(int $clientId, ?int $driverId, string $tracking, string $destination, float $distance, int $price, string $status): void
    {
        if (!Schema::hasTable('pickup_requests')) {
            return;
        }

        DB::table('pickup_requests')->updateOrInsert(
            ['tracking_id' => $tracking],
            [
                'client_id' => $clientId,
                'driver_id' => $driverId,
                'notes' => 'Pickup route near ' . $destination,
                'total_distance' => $distance,
                'total_price' => $price,
                'driver_earning' => $driverId ? $price * 0.75 : null,
                'admin_margin' => $price * 0.25,
                'status' => $status,
                'assigned_at' => $driverId ? now()->subDay() : null,
                'picked_up_at' => $status === 'completed' ? now()->subHours(9) : null,
                'delivered_at' => $status === 'completed' ? now()->subHours(3) : null,
                'completed_at' => $status === 'completed' ? now()->subHours(3) : null,
                'created_at' => now()->subDays(rand(1, 8)),
                'updated_at' => now(),
            ]
        );
    }

    private function stock(int $clientId, int $warehouseId, string $product, string $batch, string $sku, int $boxes): int
    {
        DB::table('stocks')->updateOrInsert(
            ['batch_id' => $batch],
            [
                'user_id' => $clientId,
                'client_id' => $clientId,
                'warehouse_id' => $warehouseId,
                'product_name' => $product,
                'description' => 'Demo stock from Nepal supply route.',
                'unit' => 'cartons',
                'number_of_boxes' => $boxes,
                'quantity_per_box' => 24,
                'total_quantity' => $boxes * 24,
                'remaining_quantity' => $boxes * 24,
                'sku' => $sku,
                'client_code' => 'CLI-DEMO',
                'client_name' => 'Sita Enterprises Demo',
                'received_date' => now()->subDays(4)->toDateString(),
                'status' => 'in_stock',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return (int) DB::table('stocks')->where('batch_id', $batch)->value('id');
    }

    private function box(int $stockId, int $clientId, string $boxNumber): void
    {
        DB::table('boxes')->updateOrInsert(
            ['box_number' => $boxNumber],
            [
                'stock_id' => $stockId,
                'client_id' => $clientId,
                'qr_code' => 'QR-' . $boxNumber,
                'qr_code_data' => json_encode(['box' => $boxNumber, 'origin' => 'Ilam']),
                'status' => 'active',
                'description' => 'Demo tracked carton for warehouse scanning.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function equipment(int $ownerId, string $name, string $type, string $location, int $dailyRate): void
    {
        DB::table('equipment')->updateOrInsert(
            ['name' => $name],
            [
                'user_id' => $ownerId,
                'owner_id' => $ownerId,
                'type' => $type,
                'model' => 'Demo Model',
                'year' => 2021,
                'description' => 'Nepali construction equipment demo listing.',
                'daily_rate' => $dailyRate,
                'weekly_rate' => $dailyRate * 6,
                'monthly_rate' => $dailyRate * 24,
                'location' => $location,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function securityAgency(int $userId): int
    {
        DB::table('security_agencies')->updateOrInsert(
            ['user_id' => $userId],
            [
                'agency_name' => 'Kathmandu Suraksha Sewa Demo',
                'registration_number' => 'SEC-DEMO-2083',
                'license_number' => 'LIC-DEMO-7788',
                'address' => 'Baluwatar, Kathmandu',
                'phone' => '9805556677',
                'emergency_phone' => '9805556688',
                'email' => 'security.demo@kwdc.test',
                'services_offered' => 'Warehouse guard, night patrol, CCTV monitoring',
                'year_established' => '2075',
                'status' => 'approved',
                'is_verified' => true,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return (int) DB::table('security_agencies')->where('user_id', $userId)->value('id');
    }

    private function securityPersonnel(int $agencyId, string $name, string $employeeId): int
    {
        $table = Schema::hasTable('security_personnels') ? 'security_personnels' : 'security_personnel';

        DB::table($table)->updateOrInsert(
            ['employee_id' => $employeeId],
            [
                'agency_id' => $agencyId,
                'name' => $name,
                'phone' => '9806667788',
                'email' => Str::slug($name) . '@kwdc.test',
                'citizenship_number' => 'DEMO-CIT-7788',
                'address' => 'Kirtipur, Kathmandu',
                'position' => 'Senior Guard',
                'qualifications' => 'Basic fire safety, CCTV monitoring',
                'status' => 'active',
                'has_vehicle' => true,
                'vehicle_type' => 'motorbike',
                'shift_availability' => json_encode(['day', 'night']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return (int) DB::table($table)->where('employee_id', $employeeId)->value('id');
    }

    private function securityGood(int $agencyId, string $item, string $category): void
    {
        DB::table('security_goods')->updateOrInsert(
            ['agency_id' => $agencyId, 'item_name' => $item],
            [
                'category' => $category,
                'model' => 'Demo-2083',
                'specifications' => 'Rechargeable, warehouse gate use',
                'quantity_available' => 12,
                'unit_price' => 4500,
                'is_rental' => true,
                'rental_rate_per_day' => 350,
                'description' => 'Demo security equipment for warehouse assignment.',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function securityAssignment(int $warehouseId, int $agencyId, int $personnelId): void
    {
        DB::table('security_assignments')->updateOrInsert(
            ['warehouse_id' => $warehouseId, 'agency_id' => $agencyId, 'personnel_id' => $personnelId],
            [
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonth()->toDateString(),
                'shift' => 'night',
                'shift_start' => '18:00',
                'shift_end' => '06:00',
                'status' => 'active',
                'notes' => 'Night guard demo assignment for Kalimati warehouse.',
                'total_cost' => 42000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function reminder(int $userId, string $title, string $notes): void
    {
        DB::table('user_reminders')->updateOrInsert(
            ['user_id' => $userId, 'title' => $title],
            [
                'notes' => $notes,
                'starts_at' => now()->addDay(),
                'remind_at' => now()->addHours(18),
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function notification(int $userId, string $title, string $message): void
    {
        DB::table('notifications')->updateOrInsert(
            ['user_id' => $userId, 'title' => $title],
            [
                'type' => 'demo',
                'message' => $message,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
