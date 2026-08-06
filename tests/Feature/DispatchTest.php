<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DispatchOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_dispatch()
    {
        // 1. Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // 2. Send POST request (the controller likely ignores delivery_address if not stored)
        $response = $this->actingAs($client)->post(route('dispatch.store'), [
            'pickup_address' => '123 Pickup St, Kathmandu',
            'delivery_address' => '456 Delivery Ave, Kathmandu', // sent, but not stored in this table
            'total_distance' => 15,   // use the correct field name expected by your controller
            'base_price' => 500,
        ]);

        // 3. Assertions
        $response->assertRedirect(route('dispatch.index'));
        $this->assertDatabaseHas('dispatch_orders', [
            'client_id' => $client->id,
            'pickup_address' => '123 Pickup St, Kathmandu',
            'status' => 'pending',
            'total_distance' => 15,
            'base_price' => 500,
        ]);

        $dispatch = DispatchOrder::first();
        $this->assertNotNull($dispatch->tracking_id);
    }
}