<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalClaim extends Model
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
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}