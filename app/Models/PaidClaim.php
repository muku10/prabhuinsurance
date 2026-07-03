<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaidClaim extends Model
{
    protected $fillable = [
        'import_log_id',
        'fiscal_year',
        'month',
        'department',
        'class',
        'province',
        'district',
        'branch_name',
        'total_paid_amount',
        'turnaround_days',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'total_paid_amount' => 'decimal:2',
            'turnaround_days' => 'integer',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}