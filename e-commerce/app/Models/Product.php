<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const MODERATION_CLEAR = 'clear';

    public const MODERATION_TAKEN_DOWN = 'taken_down';

    public const MODERATION_RESTORED = 'restored';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'full_description',
        'category',
        'price',
        'cover_image_path',
        'file_path',
        'status',
        'affiliate_enabled',
        'affiliate_commission_rate',
        'moderation_status',
        'moderation_notes',
        'taken_down_at',
    ];

    protected function casts(): array
    {
        return [
            'affiliate_enabled' => 'boolean',
            'affiliate_commission_rate' => 'decimal:2',
            'taken_down_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoryRecord(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function contentReports(): HasMany
    {
        return $this->hasMany(ContentReport::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function affiliateReferrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->whereIn('moderation_status', [self::MODERATION_CLEAR, self::MODERATION_RESTORED]);
    }

    public static function categories(): array
    {
        return [
            'graphics' => 'Graphics',
            'templates' => 'Templates',
            'music' => 'Music',
            'courses' => 'Courses',
            'software' => 'Software',
            'marketing' => 'Marketing',
        ];
    }

    public function coverImageUrl(): ?string
    {
        if (! $this->cover_image_path) {
            return null;
        }

        if (Str::startsWith($this->cover_image_path, ['http://', 'https://'])) {
            return $this->cover_image_path;
        }

        if (Str::startsWith($this->cover_image_path, ['images/', 'img/'])) {
            return asset($this->cover_image_path);
        }

        if (Storage::disk('public')->exists($this->cover_image_path)) {
            return Storage::disk('public')->url($this->cover_image_path);
        }

        if (Storage::exists($this->cover_image_path)) {
            return route('storage.local', ['path' => $this->cover_image_path]);
        }

        return null;
    }
}
