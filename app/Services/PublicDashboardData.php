<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Complain;
use App\Models\FinancialHighlightImport;
use App\Models\OutstandingClaim;
use App\Models\IntimationClaim;
use App\Models\ImportLog;
use App\Models\PaidClaim;
use App\Models\Policy;
use App\Models\Province;
use App\Models\WithdrawalClaim;
use App\Support\NepaliFiscalCalendar;
use Illuminate\Support\Collection;

class PublicDashboardData
{
    public function toArray(): array
    {
        $provinces = Province::with(['districts' => fn ($query) => $query->orderBy('district_name')])
            ->orderBy('province_name')
            ->get();

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
            'outstandingClaims' => $this->outstandingClaimRows(),
            'portfolioClaimRows' => $this->portfolioClaimRows(),
            'branchNetworkRows' => $this->branchNetworkRows()->all(),
            'totalProvinceCount' => $provinces->count(),
            'financialHighlights' => $financialHighlights->all(),
            'latestFinancialFiscalYear' => $latestFinancialHighlightImport?->fiscal_year,
            'latestFinancialQuarter' => $latestFinancialHighlightImport?->quarter,
            'grievanceReports' => $grievanceReports->all(),
        ];
    }

    private function portfolioClaimRows(): array
    {
        $policyLookup = $this->policyLookup();
        $rows = collect();

        $append = function ($claims, string $type, ?string $amountColumn = null, ?string $turnaroundColumn = null) use ($rows, $policyLookup): void {
            foreach ($claims as $claim) {
                $policy = $policyLookup->get((string) $claim->department);
                $rows->push([
                    'type' => $type,
                    'fiscal_year' => (string) $claim->fiscal_year,
                    'month' => (int) $claim->month,
                    'province' => $claim->province,
                    'district' => $claim->district,
                    // Department is the policy/portfolio; class is its sub-policy.
                    'portfolio' => $policy?->policy_name ?? $claim->department ?? 'Other',
                    'sub_policy' => $claim->class,
                    'amount' => $amountColumn ? (float) $claim->{$amountColumn} : 0,
                    'turnaround_days' => $turnaroundColumn ? (int) $claim->{$turnaroundColumn} : 0,
                ]);
            }
        };

        $append(IntimationClaim::query()->whereIn('import_log_id', $this->latestMonthlyImportIds('intimation_claim'))->get(), 'intimation');
        $append(OutstandingClaim::query()->whereIn('import_log_id', $this->latestMonthlyImportIds('outstanding_claim'))->get(), 'outstanding', 'amount');
        $append(PaidClaim::query()->whereIn('import_log_id', $this->latestMonthlyImportIds('paid_claim'))->get(), 'paid', 'total_paid_amount', 'turnaround_days');
        $append(WithdrawalClaim::query()->whereIn('import_log_id', $this->latestMonthlyImportIds('withdrawal_claim'))->get(), 'withdrawal');

        return $rows
            ->groupBy(fn (array $row) => implode('|', [
                $row['type'],
                $row['fiscal_year'],
                $row['month'],
                $row['province'],
                $row['district'],
                $row['portfolio'],
            ]))
            ->map(function (Collection $group) {
                $row = $group->first();

                return array_merge($row, [
                    'count' => $group->count(),
                    'amount' => (float) $group->sum('amount'),
                    'turnaround_days' => (int) $group->sum('turnaround_days'),
                ]);
            })
            ->values()
            ->all();
    }

    private function latestMonthlyImportIds(string $uploadType): array
    {
        return ImportLog::query()
            ->where('upload_type', $uploadType)
            ->where('status', 'completed')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['id', 'fiscal_year', 'month'])
            ->unique(fn (ImportLog $import) => $import->fiscal_year.'|'.$import->month)
            ->pluck('id')
            ->all();
    }

    private function policyLookup(): Collection
    {
        return Policy::with('parent')->get()->keyBy(fn ($policy) => (string) $policy->policy_id);
    }

    private function outstandingClaimRows(): array
    {
        $policyLookup = $this->policyLookup();

        return OutstandingClaim::query()
            ->whereIn('import_log_id', $this->latestMonthlyImportIds('outstanding_claim'))
            ->get(['fiscal_year', 'month', 'province', 'district', 'department', 'class', 'development_year', 'amount'])
            ->map(function (OutstandingClaim $claim) use ($policyLookup) {
                $policy = $policyLookup->get((string) $claim->department);

                return [
                    'fiscal_year' => (string) $claim->fiscal_year,
                    'month' => (int) $claim->month,
                    'province' => $claim->province,
                    'district' => $claim->district,
                    'portfolio' => $policy?->policy_name ?? $claim->department ?? 'Other',
                    'bucket' => $this->developmentBucket($claim->development_year),
                    'amount' => (float) $claim->amount,
                ];
            })
            ->values()
            ->all();
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
