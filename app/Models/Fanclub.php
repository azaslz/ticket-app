<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fanclub extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'monthly_fee',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'monthly_fee' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(FanclubMembership::class, 'fanclub_id');
    }
}
