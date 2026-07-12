<?php

namespace App\Support;

use App\Models\ImportLog;
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
