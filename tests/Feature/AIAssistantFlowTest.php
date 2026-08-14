<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIAssistantFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_support_can_open_dispatch_form_with_pre_filled_context(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'client-ai@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/chat-support', [
            'message' => 'Create a dispatch from Kathmandu to Pokhara with pickup from Baneshwor and drop to Pokhara',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/dispatch/direct-create', $data['url']);
        $this->assertNotEmpty($data['data'] ?? []);
        $this->assertArrayHasKey('pickup_address', $data['data']);
    }

    public function test_voice_assistant_can_extract_dispatch_details_from_natural_language(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'voice-ai@example.com']);

        $response = $this->actingAs($user)->postJson('/ai/voice-assistant', [
            'message' => 'I need a pickup from Boudha to Bhaktapur with cargo weight 500 kg',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/pickup/direct-create', $data['url']);
        $this->assertNotEmpty($data['data'] ?? []);
        $this->assertArrayHasKey('pickup_address', $data['data']);
    }
}
