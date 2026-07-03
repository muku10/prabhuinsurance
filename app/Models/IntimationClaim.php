<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntimationClaim extends Model
{
    protected $fillable = [
        'import_log_id',
        'fiscal_year',
        'month',
        'province',
        'district',
        'branch',
        'department',
        'class',
        'estimated_loss',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'estimated_loss' => 'decimal:2',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}