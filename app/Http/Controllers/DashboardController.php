<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ImportLog;
use App\Models\Policy;
use App\Models\Province;
use App\Support\NepaliFiscalCalendar;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $monthNames = NepaliFiscalCalendar::monthNames();
        $recentUploads = ImportLog::latest('date')->latest('id')->limit(5)->get();
        $latestUpload = $recentUploads->first();
        $totalRecords = ImportLog::count();

        return view('dashboard', [
            'recentUploads' => $recentUploads,
            'latestUploadLabel' => $latestUpload
                ? (($monthNames[$latestUpload->month] ?? $latestUpload->month).' '.$latestUpload->fiscal_year)
                : 'No uploads yet',
            'latestUploadDelta' => $latestUpload
                ? 'Uploaded '.$latestUpload->created_at->diffForHumans()
                : 'Waiting for first upload',
            'totalRecords' => number_format($totalRecords),
            'totalRecordsDelta' => $totalRecords > 0
                ? '+'.$totalRecords.' tracked entries'
                : 'No tracked entries yet',
            'monthsProcessed' => ImportLog::select('fiscal_year', 'month')->distinct()->count(),
            'activeBranches' => Branch::count(),
            'provinceCount' => Province::count(),
            'currentFiscalYear' => $latestUpload?->fiscal_year ?? 'N/A',
            'lastSync' => $latestUpload?->created_at,
            'pendingUploads' => ImportLog::whereIn('status', ['pending', 'processing'])->count(),
            'failedUploads' => ImportLog::where('status', 'failed')->count(),
            'monthNames' => $monthNames,
            'policyCount' => Policy::count(),
        ]);
    }
}
