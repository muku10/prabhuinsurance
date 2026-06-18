<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $primaryKey = 'district_id';

    protected $fillable = [
        'province_id',
        'district_name',
        'code',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'district_id', 'district_id');
    }
}