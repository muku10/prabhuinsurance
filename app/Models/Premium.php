<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Premium extends Model
{
    protected $table = 'premium';

    protected $fillable = [
        'import_log_id',
        'state_id',
        'district_id',
        'fiscal_year',
        'month',
        'department',
        'class',
        'fresh_policy',
        'renewal_policy',
        'endrosement_policy',
        'gross_premium_income',
        'sum_insured',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'fresh_policy' => 'integer',
            'renewal_policy' => 'integer',
            'endrosement_policy' => 'integer',
            'gross_premium_income' => 'decimal:2',
            'sum_insured' => 'decimal:2',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'state_id', 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }
}