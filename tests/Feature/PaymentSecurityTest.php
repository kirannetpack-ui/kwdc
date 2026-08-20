<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\AdminEmailService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PaymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_initialize_payment_for_another_users_invoice(): void
    {
        $owner = User::factory()->create(['role' => 'client']);
        $otherUser = User::factory()->create(['role' => 'client']);
        $invoice = $this->invoiceFor($owner, 1500);

        $this->actingAs($otherUser)
            ->postJson(route('payment.khalti.init'), [
                'invoice_id' => $invoice->id,
                'amount' => 1500,
            ])
            ->assertNotFound();
    }

    public function test_payment_initialization_rejects_tampered_amounts(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $invoice = $this->invoiceFor($user, 1500);

        $this->actingAs($user)
            ->postJson(route('payment.khalti.init'), [
                'invoice_id' => $invoice->id,
                'amount' => 1,
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Payment amount does not match the invoice total.',
            ]);

        $this->assertDatabaseMissing('transactions', [
            'invoice_id' => $invoice->id,
        ]);
    }

    public function test_esewa_success_callback_does_not_mark_invoice_paid_without_provider_verification(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $invoice = $this->invoiceFor($user, 1500);

        Transaction::create([
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'amount' => 1500,
            'payment_method' => 'esewa',
            'transaction_id' => 'pid-123',
            'status' => 'pending',
        ]);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('verifyEsewaPayment')
                ->once()
                ->with('pid-123', 'ref-456', 1500.0)
                ->andReturn([
                    'success' => false,
                    'message' => 'Provider rejected the transaction.',
                ]);
        });

        $this->actingAs($user)
            ->get(route('payment.esewa.success', [
                'pid' => 'pid-123',
                'refId' => 'ref-456',
            ]))
            ->assertRedirect(route('payment.failure', absolute: false));

        $this->assertSame('unpaid', $invoice->fresh()->payment_status);
        $this->assertDatabaseHas('transactions', [
            'invoice_id' => $invoice->id,
            'transaction_id' => 'pid-123',
            'status' => 'failed',
        ]);
    }

    public function test_khalti_verify_fails_when_local_transaction_is_missing(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('verifyKhaltiPayment')->never();
        });

        $this->actingAs($user)
            ->get(route('payment.khalti.verify', ['pidx' => 'unknown-pidx']))
            ->assertRedirect(route('payment.failure', absolute: false));
    }

    public function test_completed_khalti_callback_is_idempotent_without_resending_notifications(): void
    {
        Mail::fake();

        $user = User::factory()->create(['role' => 'client']);
        $invoice = $this->invoiceFor($user, 1500);
        $invoice->markAsPaid('khalti');

        Transaction::create([
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'amount' => 1500,
            'payment_method' => 'khalti',
            'transaction_id' => 'completed-pidx',
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('verifyKhaltiPayment')->never();
        });

        $this->mock(AdminEmailService::class, function ($mock) {
            $mock->shouldReceive('notifyPaymentReceived')->never();
        });

        $this->actingAs($user)
            ->get(route('payment.khalti.verify', ['pidx' => 'completed-pidx']))
            ->assertRedirect(route('payment.success', absolute: false));

        Mail::assertNothingSent();
    }

    public function test_esewa_success_callback_requires_provider_reference(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $invoice = $this->invoiceFor($user, 1500);

        Transaction::create([
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'amount' => 1500,
            'payment_method' => 'esewa',
            'transaction_id' => 'pid-without-ref',
            'status' => 'pending',
        ]);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('verifyEsewaPayment')->never();
        });

        $this->actingAs($user)
            ->get(route('payment.esewa.success', ['pid' => 'pid-without-ref']))
            ->assertRedirect(route('payment.failure', absolute: false));

        $this->assertSame('unpaid', $invoice->fresh()->payment_status);
    }

    public function test_payment_provider_routes_are_rate_limited(): void
    {
        foreach ([
            'payment.khalti.init',
            'payment.khalti.verify',
            'payment.esewa.init',
            'payment.esewa.success',
            'payment.esewa.failure',
        ] as $routeName) {
            $middleware = Route::getRoutes()->getByName($routeName)->gatherMiddleware();

            $this->assertNotEmpty(
                preg_grep('/^throttle:/', $middleware),
                "Expected {$routeName} to include throttle middleware."
            );
        }
    }

    private function invoiceFor(User $user, float $amount): Invoice
    {
        $warehouse = Warehouse::factory()->create([
            'user_id' => $user->id,
            'location' => 'Kathmandu',
        ]);

        $warehouseRequestId = DB::table('warehouse_requests')->insertGetId([
            'client_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $invoiceId = DB::table('invoices')->insertGetId([
            'user_id' => $user->id,
            'client_id' => $user->id,
            'warehouse_request_id' => $warehouseRequestId,
            'invoice_number' => 'INV-TEST-' . $user->id . '-' . random_int(1000, 9999),
            'amount' => $amount,
            'subtotal' => $amount,
            'grand_total' => $amount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'due_date' => now()->addDays(7)->toDateString(),
            'payment_due_date' => now()->addDays(7)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Invoice::findOrFail($invoiceId);
    }
}
