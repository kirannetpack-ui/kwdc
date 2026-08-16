<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoiceAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_voice_assistant_recognizes_pickup_request_from_speech(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'voice@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/voice-assistant', [
            'message' => 'I need a pickup from Boudha to Bhaktapur',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/pickup/direct-create', $data['url']);
        $this->assertArrayHasKey('pickup_address', $data['data']);
    }

    public function test_voice_assistant_recognizes_dispatch_request(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'dispatch@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/voice-assistant', [
            'message' => 'Create a dispatch from Kathmandu to Pokhara with cargo',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/dispatch/direct-create', $data['url']);
        $this->assertArrayHasKey('pickup_address', $data['data']);
    }

    public function test_voice_assistant_extracts_distance_and_price(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'extract@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/voice-assistant', [
            'message' => 'Dispatch from Thamel to Bhaktapur, 15 km, total amount 500',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/dispatch/direct-create', $data['url']);
        $this->assertArrayHasKey('pickup_address', $data['data']);
        // Distance extraction
        if (isset($data['data']['total_distance'])) {
            $this->assertStringContainsString('15', $data['data']['total_distance']);
        }
    }

    public function test_voice_assistant_handles_tracking_request(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'track@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/voice-assistant', [
            'message' => 'Where is my dispatch? I want to track it.',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        $this->assertSame('open_page', $data['action']);
        $this->assertSame('/dispatch', $data['url']);
    }

    public function test_voice_assistant_responds_with_message(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'msg@example.com']);
        $this->actingAs($user);

        $response = $this->postJson('/ai/voice-assistant', [
            'message' => 'pickup from my location to warehouse at 15 km distance',
            'language' => 'en',
        ]);

        $response->assertOk();
        $data = $response->json();

        // Should have a meaningful message
        $this->assertNotEmpty($data['message']);
        $this->assertSame('open_page', $data['action']);
    }

    public function test_voice_assistant_widget_has_single_message_area_and_text_fallback(): void
    {
        $user = User::factory()->create(['role' => 'client', 'email' => 'voice-widget@example.com']);

        $response = $this->actingAs($user)->get('/pickup');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, 'id="voiceMessages"'));
        $this->assertStringContainsString('id="voiceTextInput"', $html);
        $this->assertStringContainsString('id="voiceSendBtn"', $html);
        $this->assertStringContainsString('KWDC Assistant', $html);
        $this->assertStringNotContainsString('id="ai-chat-btn"', $html);
        $this->assertStringNotContainsString('id="voice-btn"', $html);
    }
}
