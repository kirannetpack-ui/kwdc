<?php

return [
    'khalti' => [
        'secret_key' => env('KHALTI_SECRET_KEY', ''),
        'public_key' => env('KHALTI_PUBLIC_KEY', ''),
        'base_url' => env('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/'),
        'verification_url' => env('KHALTI_VERIFICATION_URL', 'https://a.khalti.com/api/v2/epayment/lookup/'),
    ],
    'esewa' => [
        'merchant_code' => env('ESEWA_MERCHANT_CODE', ''),
        'secret_key' => env('ESEWA_SECRET_KEY', ''),
        'payment_url' => env('ESEWA_PAYMENT_URL', 'https://rc.esewa.com.np/epay/main'),
        'success_url' => env('APP_URL') . '/payment/esewa/success',
        'failure_url' => env('APP_URL') . '/payment/esewa/failure',
    ],
];