<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialHighlight extends Model
{
    protected $fillable = [
        'financial_highlight_import_id', 'fiscal_year', 'quarter', 'solvency_ratio',
        'return_on_equity', 'earnings_per_share', 'net_worth', 'net_profit_margin',
        'liquidity_ratio', 'investment_yield',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(FinancialHighlightImport::class, 'financial_highlight_import_id');
    }
}
