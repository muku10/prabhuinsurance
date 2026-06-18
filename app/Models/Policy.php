<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policy extends Model
{
    protected $primaryKey = 'policy_id';

    protected $fillable = [
        'parent_id',
        'policy_name',
        'policy_name_np',
        'code',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'parent_id', 'policy_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Policy::class, 'parent_id', 'policy_id');
    }
}