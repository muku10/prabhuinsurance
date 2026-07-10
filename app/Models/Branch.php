<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    protected $table = 'branch_network';

    protected $fillable = [
        'branch_code',
        'ext_branch_code',
        'branch_name',
        'province_id',
        'district_id',
        'fiscal_year',
        'month',
        'local_level',
        'address',
        'display_name',
        'status',
        'inactive_fiscal_year',
        'inactive_month',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }
}
