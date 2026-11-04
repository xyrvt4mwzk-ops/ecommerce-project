<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    protected $fillable = [
        'affiliate_referral_id', 'user_id', 'order_id', 'ip_hash', 'session_hash',
        'user_agent', 'is_suspicious', 'suspicion_reason', 'converted_at',
    ];

    protected function casts(): array
    {
        return ['is_suspicious' => 'boolean', 'converted_at' => 'datetime'];
    }

    public function referral()
    {
        return $this->belongsTo(AffiliateReferral::class, 'affiliate_referral_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
