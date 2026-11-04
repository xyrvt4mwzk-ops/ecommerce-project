<?php

return [
    'enabled' => (bool) env('ABA_PAYWAY_ENABLED', false),
    'environment' => env('ABA_PAYWAY_ENV', 'sandbox'),
    'merchant_id' => env('ABA_PAYWAY_MERCHANT_ID'),
    'api_key' => env('ABA_PAYWAY_API_KEY'),
    'purchase_url' => env('ABA_PAYWAY_PURCHASE_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'check_url' => env('ABA_PAYWAY_CHECK_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction'),
    'currency' => env('ABA_PAYWAY_CURRENCY', 'USD'),
];
