<?php

namespace Tests\Feature;

use App\Models\DispatchOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientLogisticsFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_dispatch_and_pickup_pages_render(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'is_client' => true,
        ]);

        DispatchOrder::factory()->create([
            'client_id' => $client->id,
            'pickup_address' => 'Kalimati Tarkari Bazaar, Kathmandu',
            'delivery_address' => 'Patan Industrial Estate, Lalitpur',
            'base_price' => 2500,
            'total_distance' => 9.5,
            'status' => 'pending',
        ]);

        $this->actingAs($client)
            ->get(route('dispatch.index'))
            ->assertOk()
            ->assertSee('Dispatch Orders')
            ->assertSee('Kalimati Tarkari Bazaar');

        $this->actingAs($client)
            ->get(route('pickup.index'))
            ->assertOk()
            ->assertSee('Pickup Requests');
    }

    public function test_client_can_create_pickup_without_driver_assignment(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'is_client' => true,
            'phone' => '9800000000',
        ]);

        $response = $this->actingAs($client)->post(route('pickup.store'), [
            'pickup_address' => 'Asan Bazaar, Kathmandu',
            'destination_address' => 'Boudha, Kathmandu',
            'contact_person' => 'Sita Shrestha',
            'contact_phone' => '9811111111',
            'description' => 'Local garment cartons',
            'estimated_boxes' => 6,
            'total_distance' => 7.5,
            'total_price' => 1500,
            'bill_type' => 'regular',
        ]);

        $pickup = \App\Models\PickupRequest::first();

        $response->assertRedirect(route('pickup.show', $pickup->id));
        $this->assertDatabaseHas('pickup_requests', [
            'client_id' => $client->id,
            'pickup_address' => 'Asan Bazaar, Kathmandu',
            'destination_address' => 'Boudha, Kathmandu',
            'driver_id' => null,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('pickup_stops', [
            'pickup_request_id' => $pickup->id,
            'address' => 'Asan Bazaar, Kathmandu',
            'contact_name' => 'Sita Shrestha',
        ]);

        $this->actingAs($client)
            ->get(route('pickup.show', $pickup->id))
            ->assertOk()
            ->assertSee('Asan Bazaar')
            ->assertSee('Awaiting assignment');
    }
}
