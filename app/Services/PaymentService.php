<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Khalti Payment Initiation
     */
    public function initiateKhaltiPayment($amount, $invoiceNumber, $userId, $invoiceId)
    {
        if (blank(config('payment.khalti.secret_key'))) {
            Log::warning('Khalti payment initiation blocked because KHALTI_SECRET_KEY is not configured.');

            return [
                'success' => false,
                'message' => 'Payment provider is not configured. Please contact support.',
            ];
        }

        $payload = [
            'return_url' => route('payment.khalti.verify'),
            'website_url' => config('app.url'),
            'amount' => $amount * 100, // Khalti expects amount in paisa
            'purchase_order_id' => $invoiceNumber,
            'purchase_order_name' => 'Payment for Invoice #' . $invoiceNumber,
            'customer_info' => [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? '9800000000',
            ],
        ];

        try {
            $response = $this->providerHttp()->withHeaders([
                'Authorization' => 'Key ' . config('payment.khalti.secret_key'),
                'Content-Type' => 'application/json',
            ])->post(rtrim(config('payment.khalti.base_url'), '/') . '/epayment/initiate/', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Khalti payment initiation failed', [
                'status' => $response->status(),
                'invoice_number' => $invoiceNumber,
                'invoice_id' => $invoiceId,
                'user_id' => $userId,
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.',
            ];

        } catch (\Exception $e) {
            Log::error('Khalti payment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ];
        }
    }

    /**
     * Verify Khalti Payment
     */
    public function verifyKhaltiPayment($pidx)
    {
        if (blank(config('payment.khalti.secret_key'))) {
            Log::warning('Khalti payment verification blocked because KHALTI_SECRET_KEY is not configured.');

            return [
                'success' => false,
                'message' => 'Payment provider is not configured. Please contact support.',
            ];
        }

        try {
            $response = $this->providerHttp()->withHeaders([
                'Authorization' => 'Key ' . config('payment.khalti.secret_key'),
                'Content-Type' => 'application/json',
            ])->post(config('payment.khalti.verification_url'), [
                'pidx' => $pidx,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'status' => $data['status'] ?? 'pending',
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment verification failed.',
            ];

        } catch (\Exception $e) {
            Log::error('Khalti verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification failed. Please contact support.',
            ];
        }
    }

    /**
     * eSewa Payment Initiation
     */
    public function initiateEsewaPayment($amount, $invoiceNumber, $invoiceId)
    {
        if (blank(config('payment.esewa.merchant_code'))) {
            Log::warning('eSewa payment initiation blocked because ESEWA_MERCHANT_CODE is not configured.');

            return [
                'success' => false,
                'message' => 'Payment provider is not configured. Please contact support.',
            ];
        }

        $pid = uniqid() . '-' . time();
        
        $url = config('payment.esewa.payment_url') . '?' . http_build_query([
            'amt' => $amount,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => $amount,
            'pid' => $pid,
            'scd' => config('payment.esewa.merchant_code'),
            'su' => route('payment.esewa.success'),
            'fu' => route('payment.esewa.failure'),
        ]);

        return [
            'success' => true,
            'url' => $url,
            'pid' => $pid,
        ];
    }

    /**
     * Verify eSewa Payment
     */
    public function verifyEsewaPayment($pid, $refId, float $amount)
    {
        if (blank(config('payment.esewa.merchant_code'))) {
            Log::warning('eSewa payment verification blocked because ESEWA_MERCHANT_CODE is not configured.');

            return [
                'success' => false,
                'message' => 'Payment provider is not configured. Please contact support.',
            ];
        }

        try {
            $amount = round($amount, 2);
            $url = config('payment.esewa.verification_url');

            $response = $this->providerHttp()->asForm()->post($url, [
                'amt' => $amount,
                'pdc' => 0,
                'psc' => 0,
                'txAmt' => 0,
                'tAmt' => $amount,
                'pid' => $pid,
                'rid' => $refId,
                'scd' => config('payment.esewa.merchant_code'),
            ]);

            if ($response->successful() && stripos($response->body(), 'Success') !== false) {
                return [
                    'success' => true,
                    'data' => [
                        'pid' => $pid,
                        'refId' => $refId,
                        'amount' => $amount,
                        'provider_response' => $response->body(),
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Verification failed.',
            ];

        } catch (\Exception $e) {
            Log::error('eSewa verification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification failed. Please contact support.',
            ];
        }
    }

    private function providerHttp()
    {
        return Http::timeout(config('payment.http.timeout'))
            ->retry(
                config('payment.http.retry_times'),
                config('payment.http.retry_sleep_ms'),
                throw: false
            );
    }
}
