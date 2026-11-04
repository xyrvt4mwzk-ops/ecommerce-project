<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'rating', 'body', 'creator_response', 'status',
        'creator_responded_at', 'flagged_at', 'moderation_notes',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'creator_responded_at' => 'datetime',
            'flagged_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->hasMany(ContentReport::class);
    }
}
