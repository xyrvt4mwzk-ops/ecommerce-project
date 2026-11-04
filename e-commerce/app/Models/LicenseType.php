<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    protected $fillable = ['name', 'slug', 'rules', 'activation_limit', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
