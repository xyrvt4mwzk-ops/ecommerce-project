<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliate_status',
        'affiliate_fraud_flagged_at',
        'affiliate_fraud_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'affiliate_fraud_flagged_at' => 'datetime',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function affiliateReferrals()
    {
        return $this->hasMany(AffiliateReferral::class, 'affiliate_user_id');
    }

    public function affiliateCommissions()
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_user_id');
    }

    public function affiliateClicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        $roles = $this->roles()->with('permissions:id,name')->get();

        // Existing installations originally shipped with role-only access. Keep
        // those roles usable until permissions are assigned, then enforce the
        // configured permission set as a subtractive filter.
        if ($roles->flatMap->permissions->isEmpty()) {
            return true;
        }

        return $roles->flatMap->permissions->contains('name', $permission);
    }

    public function primaryRole(): ?string
    {
        return $this->roles()->pluck('name')->first();
    }

    public function assignPrimaryRole(string $roleName): void
    {
        $role = Role::firstWhere('name', $roleName);

        if (! $role) {
            abort(404, 'The selected role is not available.');
        }

        $this->roles()->sync([$role->id]);
    }
}
