<?php

namespace Tests\Feature;

use App\Models\SecurityAgency;
use App\Models\User;
use App\Models\Warehouse;
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
}
