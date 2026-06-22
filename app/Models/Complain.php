<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complain extends Model
{
    protected $fillable = [
        'import_log_id',
        'year',
        'month',
        'complain_type',
        'received_num',
        'resolved_num',
        'pending_num',
        'average_resolution_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'average_resolution_time' => 'decimal:2',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}