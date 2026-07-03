<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportLog extends Model
{
    protected $fillable = [
        'date',
        'user_id',
        'upload_type',
        'file_name',
        'fiscal_year',
        'month',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Premium::class);
    }

    public function premiums(): HasMany
    {
        return $this->hasMany(Premium::class);
    }

    public function intimationClaims(): HasMany
    {
        return $this->hasMany(IntimationClaim::class);
    }

    public function paidClaims(): HasMany
    {
        return $this->hasMany(PaidClaim::class);
    }

    public function withdrawalClaims(): HasMany
    {
        return $this->hasMany(WithdrawalClaim::class);
    }

    public function outstandingClaims(): HasMany
    {
        return $this->hasMany(OutstandingClaim::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function complains(): HasMany
    {
        return $this->hasMany(Complain::class);
    }
}