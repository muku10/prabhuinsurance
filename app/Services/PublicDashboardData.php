<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Complain;
use App\Models\FinancialHighlightImport;
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

        $financialHighlightImports = FinancialHighlightImport::with('highlights')
            ->where('status', 'completed')
            ->latest('imported_at')
            ->latest('id')
            ->get();
        $latestFinancialHighlightImport = $financialHighlightImports->first();
        $financialHighlights = $financialHighlightImports
            ->map(function (FinancialHighlightImport $import) {
                $highlight = $import->highlights->first();
                if (! $highlight) {
                    return null;
                }

                return [
                    'fiscal_year' => $import->fiscal_year,
                    'quarter' => (int) $import->quarter,
                    'solvency_ratio' => $highlight->solvency_ratio,
                    'return_on_equity' => $highlight->return_on_equity,
                    'earnings_per_share' => $highlight->earnings_per_share,
                    'net_worth' => $highlight->net_worth,
                    'net_profit_margin' => $highlight->net_profit_margin,
                    'liquidity_ratio' => $highlight->liquidity_ratio,
                    'investment_yield' => $highlight->investment_yield,
                    'imported_at' => $import->imported_at?->toIso8601String(),
                ];
            })
            ->filter()
            ->unique(fn (array $row) => $row['fiscal_year'].'-'.$row['quarter'])
            ->values();
        $fiscalYears = NepaliFiscalCalendar::fiscalYearOptions()
            ->merge($financialHighlightImports->pluck('fiscal_year'))
            ->unique()
            ->sortDesc()
            ->values();
        $grievanceReports = Complain::with(['importLog', 'grievanceType'])
            ->get()
            ->groupBy(function (Complain $grievance) {
                $fiscalYear = $grievance->importLog?->fiscal_year
                    ?? $grievance->year.'-'.substr((string) ($grievance->year + 1), -2);
                $month = (int) ($grievance->importLog?->month ?? $grievance->month);

                return $fiscalYear.'|'.$month;
            })
            ->map(function (Collection $rows, string $period) {
                [$fiscalYear, $month] = explode('|', $period);
                $received = (int) $rows->sum('received_num');
                $resolved = min($received, (int) $rows->sum('resolved_num'));
                $averageResolutionTime = $rows->pluck('average_resolution_time')
                    ->first(fn ($value) => $value !== null && $value !== '');

                return [
                    'fiscal_year' => $fiscalYear,
                    'month' => (int) $month,
                    'received' => $received,
                    'resolved' => $resolved,
                    'pending' => $received - $resolved,
                    'resolution_rate' => $received > 0 ? round(($resolved / $received) * 100, 2) : 0,
                    'average_resolution_time' => $averageResolutionTime !== null ? (float) $averageResolutionTime : null,
                    'reasons' => $rows->groupBy(fn (Complain $row) => $row->grievanceType?->name ?? 'Unknown Grievance Type')
                        ->map(fn (Collection $reasonRows) => (int) $reasonRows->sum('received_num'))
                        ->sortDesc()
                        ->all(),
                ];
            })
            ->values();
        $fiscalYears = $fiscalYears
            ->merge($grievanceReports->pluck('fiscal_year'))
            ->unique()
            ->sortDesc()
            ->values();

        return [
            'fiscalYears' => $fiscalYears->map(fn ($year) => (string) $year)->values()->all(),
            // Public reporting follows the Nepal fiscal year: Shrawan first, Asar last.
            'months' => NepaliFiscalCalendar::fiscalMonthNames(),
            'provinces' => $provinces->pluck('province_name')->values()->all(),
            'districtsByProvince' => $provinces
                ->mapWithKeys(fn ($province) => [
                    $province->province_name => $province->districts->pluck('district_name')->values(),
                ])
                ->all(),
            'outstandingClaimCounts' => $outstandingClaimCounts->all(),
            'outstandingClaimAmounts' => $outstandingClaimAmounts->all(),
            'branchNetworkRows' => $this->branchNetworkRows()->all(),
            'totalProvinceCount' => $provinces->count(),
            'financialHighlights' => $financialHighlights->all(),
            'latestFinancialFiscalYear' => $latestFinancialHighlightImport?->fiscal_year,
            'latestFinancialQuarter' => $latestFinancialHighlightImport?->quarter,
            'grievanceReports' => $grievanceReports->all(),
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
