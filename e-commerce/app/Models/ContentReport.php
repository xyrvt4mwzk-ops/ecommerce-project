<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentReport extends Model
{
    protected $fillable = [
        'product_id', 'review_id', 'reporter_id', 'reviewer_id', 'type', 'reason',
        'evidence', 'status', 'admin_notes', 'counter_notice', 'resolution',
        'taken_down_at', 'countered_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'taken_down_at' => 'datetime',
            'countered_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
