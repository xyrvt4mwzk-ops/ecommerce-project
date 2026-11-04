<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'affiliate_referral_id',
        'affiliate_click_id',
        'subtotal',
        'total',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function license(): HasMany
    {
        return $this->licenses();
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferral::class, 'affiliate_referral_id');
    }

    public function affiliateClick(): BelongsTo
    {
        return $this->belongsTo(AffiliateClick::class, 'affiliate_click_id');
    }
}
