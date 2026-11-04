<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_referral_id',
        'affiliate_click_id',
        'affiliate_user_id',
        'order_id',
        'payout_id',
        'amount',
        'rate',
        'status',
        'approved_at',
        'fraud_flagged_at',
        'fraud_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rate' => 'decimal:2',
            'approved_at' => 'datetime',
            'fraud_flagged_at' => 'datetime',
        ];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferral::class, 'affiliate_referral_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(AffiliateClick::class, 'affiliate_click_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
