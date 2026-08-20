<?php

namespace Tests\Feature;

use App\Models\SecurityAgency;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Box;
use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateDocumentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_owner_can_download_their_private_document(): void
    {
        Storage::fake('private_uploads');

        $owner = User::factory()->create(['role' => 'property_owner']);
        $path = 'warehouses/documents/owner-proof.pdf';
        Storage::disk('private_uploads')->put($path, 'private warehouse document');

        Warehouse::factory()->create([
            'user_id' => $owner->id,
            'location' => 'Kathmandu',
            'ownership_document' => $path,
        ]);

        $this->actingAs($owner)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertOk();
    }

    public function test_other_users_cannot_download_private_documents(): void
    {
        Storage::fake('private_uploads');

        $owner = User::factory()->create(['role' => 'property_owner']);
        $otherUser = User::factory()->create(['role' => 'client']);
        $path = 'warehouses/documents/owner-proof.pdf';
        Storage::disk('private_uploads')->put($path, 'private warehouse document');

        Warehouse::factory()->create([
            'user_id' => $owner->id,
            'location' => 'Kathmandu',
            'ownership_document' => $path,
        ]);

        $this->actingAs($otherUser)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertForbidden();
    }

    public function test_admin_can_download_security_agency_private_document(): void
    {
        Storage::fake('private_uploads');

        $admin = User::factory()->create(['role' => 'admin']);
        $agencyUser = User::factory()->create(['role' => 'security_agency']);
        $path = 'security-documents/registration.pdf';
        Storage::disk('private_uploads')->put($path, 'private agency document');

        SecurityAgency::create([
            'user_id' => $agencyUser->id,
            'agency_name' => 'Private Docs Security',
            'registration_number' => 'PDS-001',
            'registration_certificate_path' => $path,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertOk();
    }

    public function test_private_document_route_rejects_path_traversal(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/private-documents/../.env')
            ->assertStatus(400);
    }

    public function test_box_client_can_download_their_private_document(): void
    {
        Storage::fake('private_uploads');

        $client = User::factory()->create(['role' => 'client']);
        $warehouse = Warehouse::factory()->create();
        $path = 'boxes/documents/invoices/client-invoice.pdf';
        Storage::disk('private_uploads')->put($path, 'private box invoice');

        Box::create([
            'batch_number' => 'BATCH-CLIENT-001',
            'box_number' => 1,
            'qr_code' => 'box-client-qr',
            'warehouse_id' => $warehouse->id,
            'client_id' => $client->id,
            'invoice_document' => $path,
        ]);

        $this->actingAs($client)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertOk();
    }

    public function test_warehouse_owner_can_download_box_private_document(): void
    {
        Storage::fake('private_uploads');

        $owner = User::factory()->create(['role' => 'property_owner']);
        $client = User::factory()->create(['role' => 'client']);
        $warehouse = Warehouse::factory()->create([
            'user_id' => $owner->id,
        ]);
        $path = 'boxes/documents/packing_lists/owner-access.pdf';
        Storage::disk('private_uploads')->put($path, 'private box packing list');

        Box::create([
            'batch_number' => 'BATCH-OWNER-001',
            'box_number' => 1,
            'qr_code' => 'box-owner-qr',
            'warehouse_id' => $warehouse->id,
            'client_id' => $client->id,
            'packing_list_document' => $path,
        ]);

        $this->actingAs($owner)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertOk();
    }

    public function test_other_users_cannot_download_box_private_documents(): void
    {
        Storage::fake('private_uploads');

        $client = User::factory()->create(['role' => 'client']);
        $otherUser = User::factory()->create(['role' => 'client']);
        $warehouse = Warehouse::factory()->create();
        $path = 'boxes/documents/insurance/private-insurance.pdf';
        Storage::disk('private_uploads')->put($path, 'private box insurance');

        Box::create([
            'batch_number' => 'BATCH-OTHER-001',
            'box_number' => 1,
            'qr_code' => 'box-other-qr',
            'warehouse_id' => $warehouse->id,
            'client_id' => $client->id,
            'insurance_document' => $path,
        ]);

        $this->actingAs($otherUser)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertForbidden();
    }

    public function test_equipment_owner_can_download_their_private_document(): void
    {
        Storage::fake('private_uploads');

        $owner = User::factory()->create(['role' => 'equipment_owner']);
        $path = 'equipment/documents/registration.pdf';
        Storage::disk('private_uploads')->put($path, 'private equipment document');

        Equipment::create([
            'owner_id' => $owner->id,
            'name' => 'Excavator',
            'type' => 'excavator',
            'location' => 'Kathmandu',
            'registration_doc' => $path,
        ]);

        $this->actingAs($owner)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertOk();
    }

    public function test_other_users_cannot_download_equipment_private_documents(): void
    {
        Storage::fake('private_uploads');

        $owner = User::factory()->create(['role' => 'equipment_owner']);
        $otherUser = User::factory()->create(['role' => 'client']);
        $path = 'equipment/documents/insurance.pdf';
        Storage::disk('private_uploads')->put($path, 'private equipment insurance');

        Equipment::create([
            'owner_id' => $owner->id,
            'name' => 'Loader',
            'type' => 'loader',
            'location' => 'Lalitpur',
            'insurance_doc' => $path,
        ]);

        $this->actingAs($otherUser)
            ->get(route('documents.private.show', ['path' => $path]))
            ->assertForbidden();
    }

    public function test_box_documents_endpoint_returns_private_urls_only_to_authorized_users(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $otherUser = User::factory()->create(['role' => 'client']);
        $warehouse = Warehouse::factory()->create();

        $box = Box::create([
            'batch_number' => 'BATCH-DOCS-001',
            'box_number' => 1,
            'qr_code' => 'box-docs-qr',
            'warehouse_id' => $warehouse->id,
            'client_id' => $client->id,
            'invoice_document' => 'boxes/documents/invoices/private-invoice.pdf',
            'other_documents' => ['boxes/documents/others/private-extra.pdf'],
        ]);

        $response = $this->actingAs($client)
            ->getJson(route('boxes.documents', $box->id))
            ->assertOk();

        $response->assertJsonPath('invoice_document', route('documents.private.show', ['path' => 'boxes/documents/invoices/private-invoice.pdf']));
        $response->assertJsonPath('other_documents.0', route('documents.private.show', ['path' => 'boxes/documents/others/private-extra.pdf']));
        $this->assertStringNotContainsString('/storage/', $response->getContent());

        $this->actingAs($otherUser)
            ->getJson(route('boxes.documents', $box->id))
            ->assertForbidden();
    }
}
