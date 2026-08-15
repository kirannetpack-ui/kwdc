<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_debug_routes_are_not_registered(): void
    {
        $this->get('/test-login')->assertNotFound();
        $this->get('/clear-cache')->assertNotFound();
        $this->get('/test-email')->assertNotFound();
    }

    public function test_notification_email_delivery_fields_are_mass_assignable(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'is_active' => true]);

        $notification = Notification::create([
            'notification_number' => 'NOT-TEST-001',
            'type' => 'dispatch_created',
            'title' => 'Dispatch created',
            'subject' => 'Dispatch created',
            'message' => 'A dispatch was created.',
            'user_id' => $user->id,
            'recipient_email' => $user->email,
            'recipient_name' => $user->name,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->assertSame('sent', $notification->status);
        $this->assertDatabaseHas('notifications', [
            'notification_number' => 'NOT-TEST-001',
            'recipient_email' => $user->email,
        ]);
    }
}
