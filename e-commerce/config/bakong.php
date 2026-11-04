<?php

return [
    'environment' => env('BAKONG_ENV', 'production'),
    'api_token' => env('BAKONG_API_TOKEN'),
    'account_id' => env('BAKONG_ACCOUNT_ID'),
    'account_information' => env('BAKONG_ACCOUNT_INFORMATION'),
    'acquiring_bank' => env('BAKONG_ACQUIRING_BANK'),
    'merchant_name' => env('BAKONG_MERCHANT_NAME'),
    'merchant_city' => env('BAKONG_MERCHANT_CITY', 'Phnom Penh'),
    'currency' => env('BAKONG_CURRENCY', 'USD'),
    'api_base_url' => env('BAKONG_API_BASE_URL', 'https://api-bakong.nbc.gov.kh'),
    'lifetime_minutes' => (int) env('BAKONG_QR_LIFETIME', 10),
];
