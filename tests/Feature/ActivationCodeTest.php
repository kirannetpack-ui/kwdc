<?php

namespace Tests\Feature;

use App\Mail\ActivationCodeMail;
use App\Models\User;
use App\Services\ActivationCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ActivationCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_activate_with_valid_code(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create(['is_active' => true]);
        $code = app(ActivationCodeService::class)->generateFor($user);

        $response = $this->post(route('activation.verify'), [
            'email' => $user->email,
            'activation_code' => $code,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->activation_code_hash);
    }

    public function test_activation_email_has_professional_verification_details(): void
    {
        $user = User::factory()->unverified()->create([
            'name' => 'Kiran',
            'email' => 'kiran@example.com',
        ]);

        $mail = new ActivationCodeMail($user, '123456');
        $html = $mail->render();

        $mail->assertHasSubject('Your KTM-WDC verification code');
        $this->assertStringContainsString('Email verification', $html);
        $this->assertStringContainsString('123456', $html);
        $this->assertStringContainsString('This code expires in 30 minutes.', $html);
        $this->assertStringContainsString('Open activation page', $html);
        $this->assertStringContainsString('KTM-WDC will never ask for your password or payment details', $html);
    }
}
