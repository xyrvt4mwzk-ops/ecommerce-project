<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'status',
        'amount',
        'currency',
        'provider',
        'provider_reference',
        'khqr_md5',
        'khqr_payload',
        'transaction_id',
        'paid_at',
        'verified_at',
        'expires_at',
        'verification_response',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verification_response' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function hasActiveKhqr(): bool
    {
        return $this->status === 'pending'
            && filled($this->khqr_payload)
            && $this->expires_at?->isFuture() === true;
    }

    public function canRetry(): bool
    {
        return in_array($this->status, ['cancelled', 'failed', 'expired'], true)
            || ($this->status === 'pending' && ! $this->hasActiveKhqr());
    }
}
