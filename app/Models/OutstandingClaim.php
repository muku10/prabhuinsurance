<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutstandingClaim extends Model
{
    protected $fillable = [
        'import_log_id',
        'fiscal_year',
        'month',
        'development_year',
        'province',
        'district',
        'branch',
        'department',
        'class',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}
