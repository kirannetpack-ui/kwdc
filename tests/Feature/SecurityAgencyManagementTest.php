<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\SecurityAgency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAgencyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_agency_can_create_personnel_and_record_notification(): void
    {
        $user = User::factory()->create([
            'role' => 'security_agency',
            'email' => 'agency@example.com',
        ]);

        SecurityAgency::create([
            'user_id' => $user->id,
            'agency_name' => 'Alpha Guard',
            'email' => 'agency@example.com',
            'phone' => '9800000000',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->post('/security/personnel', [
            'name' => 'Ram Shrestha',
            'phone' => '9876543210',
            'email' => 'ram@example.com',
            'position' => 'Senior Guard',
            'status' => 'active',
        ]);

        $response->assertRedirect('/security/personnel');
        $this->assertDatabaseHas('security_personnels', ['name' => 'Ram Shrestha']);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'security_personnel_created']);
    }

    public function test_security_agency_can_update_and_delete_personnel_and_record_notifications(): void
    {
        $user = User::factory()->create(['role' => 'security_agency']);
        $agency = SecurityAgency::create([
            'user_id' => $user->id,
            'agency_name' => 'Bravo Guard',
            'email' => $user->email,
            'phone' => '9800000001',
            'status' => 'approved',
        ]);

        $personnel = $agency->personnel()->create([
            'name' => 'Old Name',
            'phone' => '9800000002',
            'email' => 'old@example.com',
            'position' => 'Guard',
            'status' => 'active',
        ]);

        $updateResponse = $this->actingAs($user)->put('/security/personnel/' . $personnel->id, [
            'name' => 'Updated Name',
            'phone' => '9800000002',
            'email' => 'updated@example.com',
            'position' => 'Supervisor',
            'status' => 'active',
        ]);

        $updateResponse->assertRedirect('/security/personnel');
        $this->assertDatabaseHas('security_personnels', ['id' => $personnel->id, 'name' => 'Updated Name']);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'security_personnel_updated']);

        $deleteResponse = $this->actingAs($user)->delete('/security/personnel/' . $personnel->id);

        $deleteResponse->assertRedirect('/security/personnel');
        $this->assertDatabaseMissing('security_personnels', ['id' => $personnel->id]);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'security_personnel_deleted']);
    }

    public function test_security_agency_can_manage_goods_and_assignments_with_notifications(): void
    {
        $user = User::factory()->create(['role' => 'security_agency', 'email' => 'agency2@example.com']);
        $agency = SecurityAgency::create([
            'user_id' => $user->id,
            'agency_name' => 'Charlie Guard',
            'email' => 'agency2@example.com',
            'phone' => '9800000003',
            'status' => 'approved',
        ]);

        $warehouse = \App\Models\Warehouse::create([
            'user_id' => $user->id,
            'name' => 'Main Warehouse',
            'location' => 'Kathmandu',
            'address' => 'Test Street 1',
            'latitude' => 27.7172,
            'longitude' => 85.3240,
            'status' => 'approved',
        ]);

        $goodResponse = $this->actingAs($user)->post('/security/goods', [
            'item_name' => 'Guard Gear Kit',
            'category' => 'Equipment',
            'quantity_available' => 10,
            'unit_price' => 150.00,
            'status' => 'available',
        ]);

        $goodResponse->assertRedirect('/security/goods');
        $this->assertDatabaseHas('security_goods', ['item_name' => 'Guard Gear Kit']);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'security_good_created']);

        $personnel = $agency->personnel()->create([
            'name' => 'Assignment Guard',
            'phone' => '9800000004',
            'position' => 'Guard',
            'status' => 'active',
        ]);

        $assignmentResponse = $this->actingAs($user)->post('/security/assignments', [
            'warehouse_id' => $warehouse->id,
            'personnel_id' => $personnel->id,
            'start_date' => '2026-08-15',
            'shift' => 'day',
            'status' => 'active',
            'total_cost' => 500,
        ]);

        $assignmentResponse->assertRedirect('/security/assignments');
        $this->assertDatabaseHas('security_assignments', ['warehouse_id' => $warehouse->id, 'agency_id' => $agency->id]);
        $this->assertDatabaseHas('notifications', ['user_id' => $user->id, 'type' => 'security_assignment_created']);
    }
}
