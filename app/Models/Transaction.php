<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'import_log_id',
        'import_batch_token',
        'state_id',
        'district_id',
        'static_policies_id',
        'static_sub_policies_id',
        'fiscal_year',
        'month',
        'number_of_issued_policy',
        'as_on_issued_policy',
        'gross_premium_income',
        'sum_insured',
        'number_of_gross_claim',
        'amount_of_gross_claim',
        'number_of_gross_claim_paid',
        'amount_of_gross_claim_paid',
        'number_of_outstanding_claim',
        'amount_of_outstanding_claim',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'number_of_issued_policy' => 'integer',
            'as_on_issued_policy' => 'integer',
            'gross_premium_income' => 'decimal:2',
            'sum_insured' => 'decimal:2',
            'number_of_gross_claim' => 'integer',
            'amount_of_gross_claim' => 'decimal:2',
            'number_of_gross_claim_paid' => 'integer',
            'amount_of_gross_claim_paid' => 'decimal:2',
            'number_of_outstanding_claim' => 'integer',
            'amount_of_outstanding_claim' => 'decimal:2',
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

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'static_policies_id', 'policy_id');
    }

    public function subPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'static_sub_policies_id', 'policy_id');
    }
}
