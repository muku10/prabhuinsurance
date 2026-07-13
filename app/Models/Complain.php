<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complain extends Model
{
    protected $table = 'grievances';

    protected $fillable = [
        'import_log_id',
        'year',
        'month',
        'grievance_type',
        'received_num',
        'resolved_num',
        'pending_num',
        'resolution_rate',
        'average_resolution_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'average_resolution_time' => 'decimal:2',
            'resolution_rate' => 'decimal:2',
        ];
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }

    public function grievanceType(): BelongsTo
    {
        return $this->belongsTo(GrievanceType::class, 'grievance_type', 'id');
    }
}
