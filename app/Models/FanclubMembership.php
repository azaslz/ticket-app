<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanclubMembership extends Model
{
    protected $table = 'joinfc';

    protected $fillable = [
        'account_id',
        'fanclub_id',
        'joinfc_join_day',
        'joinfc_limit',
        'joinfc_status',
    ];

    protected function casts(): array
    {
        return [
            'joinfc_join_day' => 'date',
            'joinfc_limit' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_id');
    }

    public function fanclub(): BelongsTo
    {
        return $this->belongsTo(Fanclub::class, 'fanclub_id');
    }
}
