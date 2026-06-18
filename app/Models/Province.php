<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $primaryKey = 'province_id';

    protected $fillable = [
        'province_name',
        'code',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'province_id', 'province_id');
    }
}