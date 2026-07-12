<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\OutstandingClaim;
use App\Models\Policy;
use App\Models\Province;
use App\Support\NepaliFiscalCalendar;
use Illuminate\Support\Collection;

class PublicDashboardData
{
    private const OUTSTANDING_BUCKETS = [
        'lt_1' => '< 1 yr',
        'yr_1_3' => '1-3 yr',
        'yr_3_5' => '3-5 yr',
        'yr_5_plus' => '5+ yr',
    ];

    public function toArray(): array
    {
        $provinces = Province::with(['districts' => fn ($query) => $query->orderBy('district_name')])
            ->orderBy('province_name')
            ->get();

        [$outstandingClaimCounts, $outstandingClaimAmounts] = $this->outstandingClaimTables();

        return [
            'fiscalYears' => NepaliFiscalCalendar::fiscalYearOptions(),
            'months' => NepaliFiscalCalendar::monthNames(),
            'provinces' => $provinces,
            'districtsByProvince' => $provinces
                ->mapWithKeys(fn ($province) => [
                    $province->province_name => $province->districts->pluck('district_name')->values(),
                ])
                ->all(),
            'outstandingClaimCounts' => $outstandingClaimCounts,
            'outstandingClaimAmounts' => $outstandingClaimAmounts,
            'branchNetworkRows' => $this->branchNetworkRows(),
            'totalProvinceCount' => $provinces->count(),
        ];
    }

    private function outstandingClaimTables(): array
    {
        $policyLookup = Policy::with('parent')->get()->keyBy(fn ($policy) => (string) $policy->policy_id);
        $rows = [];

        OutstandingClaim::query()
            ->get(['class', 'development_year', 'amount'])
            ->each(function (OutstandingClaim $claim) use (&$rows, $policyLookup) {
                $policy = $policyLookup->get((string) $claim->class);
                $portfolio = $policy
                    ? ($policy->parent?->policy_name ?? $policy->policy_name)
                    : 'Other';
                $bucket = $this->developmentBucket($claim->development_year);

                $rows[$portfolio] ??= $this->emptyOutstandingBuckets();
                $rows[$portfolio][$bucket]['count']++;
                $rows[$portfolio][$bucket]['amount'] += (float) $claim->amount;
            });

        ksort($rows);

        $bucketKeys = collect(self::OUTSTANDING_BUCKETS)->keys();

        return [
            $this->outstandingCountRows($rows, $bucketKeys),
            $this->outstandingAmountRows($rows, $bucketKeys),
        ];
    }

    private function emptyOutstandingBuckets(): array
    {
        return collect(self::OUTSTANDING_BUCKETS)
            ->mapWithKeys(fn ($_label, $key) => [$key => ['count' => 0, 'amount' => 0.0]])
            ->all();
    }

    private function outstandingCountRows(array $rows, Collection $bucketKeys): Collection
    {
        return collect($rows)
            ->map(fn ($buckets, $portfolio) => array_merge(
                [$portfolio],
                $bucketKeys->map(fn ($key) => $buckets[$key]['count'])->all(),
                [$bucketKeys->sum(fn ($key) => $buckets[$key]['count'])]
            ))
            ->values();
    }

    private function outstandingAmountRows(array $rows, Collection $bucketKeys): Collection
    {
        return collect($rows)
            ->map(fn ($buckets, $portfolio) => array_merge(
                [$portfolio],
                $bucketKeys->map(fn ($key) => $buckets[$key]['amount'] > 0 ? number_format($buckets[$key]['amount']) : '—')->all(),
                [$bucketKeys->sum(fn ($key) => $buckets[$key]['amount']) > 0
                    ? number_format($bucketKeys->sum(fn ($key) => $buckets[$key]['amount']))
                    : '—']
            ))
            ->values();
    }

    private function developmentBucket(?string $developmentYear): string
    {
        $value = strtolower(trim((string) $developmentYear));

        if ($value === '' || str_contains($value, '5+')) {
            return 'yr_5_plus';
        }

        if (str_contains($value, '<') || str_contains($value, 'less') || preg_match('/^0(?:\D|$)/', $value) === 1) {
            return 'lt_1';
        }

        if (preg_match('/^1\s*[-–]\s*3(?:\D|$)/', $value) === 1) {
            return 'yr_1_3';
        }

        if (preg_match('/^3\s*[-–]\s*5(?:\D|$)/', $value) === 1) {
            return 'yr_3_5';
        }

        if (preg_match('/(\d+(?:\.\d+)?)/', $value, $matches) !== 1) {
            return 'yr_5_plus';
        }

        $years = (float) $matches[1];

        return match (true) {
            $years < 1 => 'lt_1',
            $years <= 3 => 'yr_1_3',
            $years <= 5 => 'yr_3_5',
            default => 'yr_5_plus',
        };
    }

    private function branchNetworkRows(): Collection
    {
        return Branch::with(['province', 'district'])
            ->orderBy('branch_code')
            ->get()
            ->map(fn ($branch) => [
                'province' => $branch->province?->province_name,
                'district' => $branch->district?->district_name,
                'fiscal_year' => $branch->fiscal_year,
                'month' => $branch->month ? (int) $branch->month : null,
                'status' => $branch->status,
                'inactive_fiscal_year' => $branch->inactive_fiscal_year,
                'inactive_month' => $branch->inactive_month ? (int) $branch->inactive_month : null,
            ])
            ->values();
    }
}
