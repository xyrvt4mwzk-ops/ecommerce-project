<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['user_id', 'kind', 'amount', 'currency', 'status', 'reference', 'notes', 'processed_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'processed_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }
}
