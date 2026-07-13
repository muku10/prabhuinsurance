<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialHighlightImport extends Model
{
    protected $fillable = ['user_id', 'file_name', 'original_file_name', 'fiscal_year', 'quarter', 'status', 'error_message', 'imported_rows', 'imported_at'];

    protected function casts(): array
    {
        return ['quarter' => 'integer', 'imported_rows' => 'integer', 'imported_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(FinancialHighlight::class);
    }
}
