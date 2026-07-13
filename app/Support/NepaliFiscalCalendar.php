<?php

namespace App\Support;

use App\Models\ImportLog;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class NepaliFiscalCalendar
{
    public static function monthNames(): array
    {
        return [
            1 => 'Baisakh',
            2 => 'Jestha',
            3 => 'Asar',
            4 => 'Shrawan',
            5 => 'Bhadra',
            6 => 'Ashwin',
            7 => 'Kartik',
            8 => 'Mangsir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Falgun',
            12 => 'Chaitra',
        ];
    }

    public static function fiscalMonthNames(): array
    {
        $months = self::monthNames();

        return collect([4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3])
            ->mapWithKeys(fn (int $month) => [$month => $months[$month]])
            ->all();
    }

    public static function quarterForMonth(int $month): ?int
    {
        return match (true) {
            in_array($month, [4, 5, 6], true) => 1,
            in_array($month, [7, 8, 9], true) => 2,
            in_array($month, [10, 11, 12], true) => 3,
            in_array($month, [1, 2, 3], true) => 4,
            default => null,
        };
    }

    public static function quarterEndMonth(int $quarter): ?int
    {
        return [1 => 6, 2 => 9, 3 => 12, 4 => 3][$quarter] ?? null;
    }

    public static function currentPeriod(?CarbonInterface $date = null): array
    {
        $date ??= now();
        $gregorianYear = (int) $date->format('Y');
        $monthDay = $date->format('m-d');
        $bsYear = $gregorianYear + ($monthDay >= '04-14' ? 57 : 56);
        $bsMonth = match (true) {
            $monthDay >= '04-14' && $monthDay < '05-15' => 1,
            $monthDay >= '05-15' && $monthDay < '06-15' => 2,
            $monthDay >= '06-15' && $monthDay < '07-17' => 3,
            $monthDay >= '07-17' && $monthDay < '08-17' => 4,
            $monthDay >= '08-17' && $monthDay < '09-17' => 5,
            $monthDay >= '09-17' && $monthDay < '10-18' => 6,
            $monthDay >= '10-18' && $monthDay < '11-17' => 7,
            $monthDay >= '11-17' && $monthDay < '12-16' => 8,
            $monthDay >= '12-16' || $monthDay < '01-15' => 9,
            $monthDay >= '01-15' && $monthDay < '02-13' => 10,
            $monthDay >= '02-13' && $monthDay < '03-15' => 11,
            default => 12,
        };
        $fiscalYearStart = $bsMonth >= 4 ? $bsYear : $bsYear - 1;

        return [
            'fiscal_year' => $fiscalYearStart.'-'.substr((string) ($fiscalYearStart + 1), -2),
            'month' => $bsMonth,
            'year' => $bsYear,
        ];
    }

    public static function fiscalMonthOrder(int $month): ?int
    {
        $position = array_search($month, [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3], true);

        return $position === false ? null : $position + 1;
    }

    public static function fiscalYearOptions(): Collection
    {
        $fiscalYears = ImportLog::query()
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year')
            ->values();

        return $fiscalYears->isNotEmpty()
            ? $fiscalYears
            : collect(self::fallbackFiscalYears());
    }

    public static function fallbackFiscalYears(): array
    {
        return ['2082-83', '2081-82', '2080-81', '2079-80', '2078-79'];
    }
}
