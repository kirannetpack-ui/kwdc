<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
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

    public function test_source_views_and_scripts_do_not_ship_console_debug_logs(): void
    {
        $paths = [
            public_path('js/voice-assistant.js'),
            resource_path('views/warehouses/create.blade.php'),
            resource_path('views/equipment-requests/create.blade.php'),
        ];

        foreach ($paths as $path) {
            $this->assertStringNotContainsString('console.log', file_get_contents($path), $path);
        }
    }

    public function test_authentication_logs_do_not_include_session_tokens_or_raw_login_email(): void
    {
        $loginController = file_get_contents(app_path('Http/Controllers/Auth/LoginController.php'));

        $this->assertStringNotContainsString('session()->token()', $loginController);
        $this->assertStringNotContainsString('session_token', $loginController);
        $this->assertStringNotContainsString("'email' => \$request->email", $loginController);
        $this->assertStringNotContainsString('"email" => $request->email', $loginController);
    }

    public function test_security_headers_are_applied_to_https_responses(): void
    {
        $response = $this->withServerVariables(['HTTPS' => 'on'])->get('/ping');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $this->assertStringContainsString("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('microphone=(self)', $response->headers->get('Permissions-Policy'));
    }

    public function test_production_requests_are_forced_to_https(): void
    {
        Config::set('app.env', 'production');

        $this->get('http://localhost/ping')
            ->assertRedirect('https://localhost/ping');
    }

    public function test_production_environment_template_documents_secure_defaults(): void
    {
        $template = file_get_contents(base_path('.env.production.example'));

        $this->assertStringContainsString('APP_ENV=production', $template);
        $this->assertStringContainsString('APP_DEBUG=false', $template);
        $this->assertStringContainsString('SESSION_ENCRYPT=true', $template);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $template);
        $this->assertStringContainsString('MAIL_MAILER=smtp', $template);
        $this->assertStringContainsString('KHALTI_SECRET_KEY=', $template);
        $this->assertStringContainsString('ESEWA_VERIFICATION_URL=https://esewa.com.np/epay/transrec', $template);
        $this->assertStringContainsString('PAYMENT_HTTP_TIMEOUT=10', $template);
    }

    public function test_deployment_config_does_not_generate_app_key_at_build_time(): void
    {
        $nixpacks = file_get_contents(base_path('nixpacks.toml'));
        $startScript = file_get_contents(base_path('scripts/deploy/start-production.sh'));
        $procfile = file_get_contents(base_path('Procfile'));

        $this->assertStringNotContainsString('key:generate', $nixpacks);
        $this->assertStringContainsString('npm run build', $nixpacks);
        $this->assertStringContainsString('APP_KEY must be configured', $startScript);
        $this->assertStringContainsString('php artisan app:production-preflight', $startScript);
        $this->assertStringContainsString('php artisan migrate --force', $startScript);
        $this->assertLessThan(
            strpos($startScript, 'php artisan app:production-preflight'),
            strpos($startScript, 'php artisan optimize:clear')
        );
        $this->assertLessThan(
            strpos($startScript, 'php artisan migrate --force'),
            strpos($startScript, 'php artisan app:production-preflight')
        );
        $this->assertStringContainsString('worker: php artisan queue:work', $procfile);
        $this->assertStringContainsString('scheduler: php artisan schedule:work', $procfile);
    }

    public function test_apache_deployments_serve_laravel_public_directory(): void
    {
        $dockerfile = file_get_contents(base_path('dockerfile'));
        $htaccess = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('APACHE_DOCUMENT_ROOT=/var/www/html/public', $dockerfile);
        $this->assertStringContainsString('a2enmod rewrite headers', $dockerfile);
        $this->assertStringContainsString('RewriteRule ^ index.php [L]', $htaccess);
    }

    public function test_generated_private_artifacts_are_not_tracked(): void
    {
        exec('git ls-files', $trackedFiles, $exitCode);

        $this->assertSame(0, $exitCode);

        $forbiddenPatterns = [
            '#^archive/frontend-backup/node_modules/#',
            '#^database/backups/#',
            '#^archive/root-junk/cookies\.txt$#',
            '#^archive/root-junk/login\.html$#',
            '#^archive/root-junk/.+\.bak$#',
        ];

        foreach ($forbiddenPatterns as $pattern) {
            $matches = preg_grep($pattern, $trackedFiles);

            $this->assertSame([], array_values($matches), 'Forbidden tracked files matched ' . $pattern);
        }
    }

    public function test_admin_warehouse_review_links_sensitive_documents_through_private_route(): void
    {
        $view = file_get_contents(resource_path('views/admin/warehouses/show.blade.php'));

        $this->assertStringContainsString('$warehouse->tax_document', $view);
        $this->assertStringContainsString('$warehouse->fire_safety_document', $view);
        $this->assertStringContainsString("route('documents.private.show', ['path' => \$warehouse->tax_document])", $view);
        $this->assertStringContainsString("route('documents.private.show', ['path' => \$warehouse->fire_safety_document])", $view);

        $this->assertStringNotContainsString('tax_clearance_document', $view);
        $this->assertStringNotContainsString('fire_safety_certificate', $view);
        $this->assertStringNotContainsString('Storage::url($warehouse->tax_', $view);
        $this->assertStringNotContainsString('Storage::url($warehouse->fire_safety_', $view);
    }

    public function test_box_document_surfaces_do_not_use_public_storage_urls(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/BoxController.php'));
        $indexView = file_get_contents(resource_path('views/client/boxes/index.blade.php'));
        $trackView = file_get_contents(resource_path('views/client/boxes/track.blade.php'));

        $this->assertStringContainsString("'private_uploads'", $controller);
        $this->assertStringContainsString("route('documents.private.show'", $trackView);

        $this->assertStringNotContainsString("store('boxes/documents/invoices', 'public')", $controller);
        $this->assertStringNotContainsString("store('boxes/documents/packing_lists', 'public')", $controller);
        $this->assertStringNotContainsString("store('boxes/documents/insurance', 'public')", $controller);
        $this->assertStringNotContainsString("store('boxes/documents/others', 'public')", $controller);
        $this->assertStringNotContainsString('/storage/${data.', $indexView);
        $this->assertStringNotContainsString('Storage::url($box->', $trackView);
    }

    public function test_payment_provider_calls_have_timeout_retry_and_config_guards(): void
    {
        $service = file_get_contents(app_path('Services/PaymentService.php'));
        $paymentConfig = file_get_contents(config_path('payment.php'));

        $this->assertStringContainsString("'timeout' => (int) env('PAYMENT_HTTP_TIMEOUT'", $paymentConfig);
        $this->assertStringContainsString('Http::timeout(config(\'payment.http.timeout\'))', $service);
        $this->assertStringContainsString('->retry(', $service);
        $this->assertStringContainsString("blank(config('payment.khalti.secret_key'))", $service);
        $this->assertStringContainsString("blank(config('payment.esewa.merchant_code'))", $service);
    }

    public function test_auth_routes_use_single_canonical_password_reset_surface(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $uris = collect(Route::getRoutes())->map(fn ($route) => $route->uri())->all();

        $this->assertStringNotContainsString('Auth::routes()', $routes);
        $this->assertNotContains('password/reset', $uris);
        $this->assertNotContains('password/email', $uris);
        $this->assertContains('forgot-password', $uris);
        $this->assertContains('reset-password/{token}', $uris);
    }

    public function test_password_reset_uses_branded_security_email(): void
    {
        $userModel = file_get_contents(app_path('Models/User.php'));
        $emailView = file_get_contents(resource_path('views/emails/password-reset.blade.php'));

        $this->assertStringContainsString('ProfessionalResetPasswordNotification', $userModel);
        $this->assertStringContainsString('Reset your KTM-WDC password', $emailView);
        $this->assertStringContainsString('KTM-WDC will never ask you to share your password', $emailView);
        $this->assertStringContainsString('This password reset link expires in', $emailView);
    }

    public function test_production_preflight_fails_for_incomplete_configuration(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.debug', false);
        Config::set('app.key', '');
        Config::set('app.url', 'http://ktm-wdc.example');

        $exitCode = Artisan::call('app:production-preflight');
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('APP_KEY must be configured.', $output);
        $this->assertStringContainsString('APP_URL must be a real https:// URL.', $output);
    }

    public function test_production_preflight_passes_with_required_configuration(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.debug', false);
        Config::set('app.key', 'base64:production-secret-key');
        Config::set('app.url', 'https://ktm-wdc.example');
        Config::set('session.encrypt', true);
        Config::set('session.secure', true);
        Config::set('session.http_only', true);
        Config::set('session.same_site', 'lax');
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.example.com');
        Config::set('mail.mailers.smtp.username', 'mail-user');
        Config::set('mail.mailers.smtp.password', 'mail-password');
        Config::set('mail.from.address', 'no-reply@ktm-wdc.example');
        Config::set('payment.khalti.public_key', 'khalti-public');
        Config::set('payment.khalti.secret_key', 'khalti-secret');
        Config::set('payment.khalti.base_url', 'https://a.khalti.com/api/v2/');
        Config::set('payment.khalti.verification_url', 'https://a.khalti.com/api/v2/epayment/lookup/');
        Config::set('payment.esewa.merchant_code', 'esewa-merchant');
        Config::set('payment.esewa.secret_key', 'esewa-secret');
        Config::set('payment.esewa.payment_url', 'https://esewa.com.np/epay/main');
        Config::set('payment.esewa.verification_url', 'https://esewa.com.np/epay/transrec');

        $exitCode = Artisan::call('app:production-preflight');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Production preflight passed.', Artisan::output());
    }
}
