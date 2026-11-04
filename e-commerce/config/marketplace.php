<?php

return [
    'affiliate' => [
        'attribution_days' => (int) env('AFFILIATE_ATTRIBUTION_DAYS', 30),
        'duplicate_click_minutes' => (int) env('AFFILIATE_DUPLICATE_CLICK_MINUTES', 10),
        'minimum_payout' => (float) env('AFFILIATE_MINIMUM_PAYOUT', 10),
    ],
];
