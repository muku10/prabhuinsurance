<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\ImportLogController;
use App\Models\Branch;
use App\Models\District;
use App\Models\ImportLog;
use App\Models\OutstandingClaim;
use App\Models\Policy;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

$publicDashboard = function () {
    $monthNames = [
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

    $fiscalYears = ImportLog::query()
        ->whereNotNull('fiscal_year')
        ->distinct()
        ->orderByDesc('fiscal_year')
        ->pluck('fiscal_year')
        ->values();

    if ($fiscalYears->isEmpty()) {
        $fiscalYears = collect(['2082-83', '2081-82', '2080-81', '2079-80', '2078-79']);
    }

    $months = $monthNames;

    $provinces = Province::with(['districts' => fn ($query) => $query->orderBy('district_name')])
        ->orderBy('province_name')
        ->get();

    $bucketLabels = ['lt_1' => '< 1 yr', 'yr_1_3' => '1-3 yr', 'yr_3_5' => '3-5 yr', 'yr_5_plus' => '5+ yr'];
    $policyLookup = Policy::with('parent')->get()->keyBy(fn ($policy) => (string) $policy->policy_id);
    $outstandingRows = [];
    $developmentBucket = function (?string $developmentYear): string {
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
    };

    OutstandingClaim::query()
        ->get(['class', 'development_year', 'amount'])
        ->each(function (OutstandingClaim $claim) use (&$outstandingRows, $policyLookup, $bucketLabels, $developmentBucket) {
            $policy = $policyLookup->get((string) $claim->class);
            $portfolio = $policy
                ? ($policy->parent?->policy_name ?? $policy->policy_name)
                : 'Other';
            $bucket = $developmentBucket($claim->development_year);

            $outstandingRows[$portfolio] ??= collect($bucketLabels)
                ->mapWithKeys(fn ($_label, $key) => [$key => ['count' => 0, 'amount' => 0.0]])
                ->all();
            $outstandingRows[$portfolio][$bucket]['count']++;
            $outstandingRows[$portfolio][$bucket]['amount'] += (float) $claim->amount;
        });

    ksort($outstandingRows);

    $outstandingClaimCounts = collect($outstandingRows)
        ->map(fn ($buckets, $portfolio) => array_merge(
            [$portfolio],
            collect($bucketLabels)->keys()->map(fn ($key) => $buckets[$key]['count'])->all(),
            [collect($bucketLabels)->keys()->sum(fn ($key) => $buckets[$key]['count'])]
        ))
        ->values();

    $outstandingClaimAmounts = collect($outstandingRows)
        ->map(fn ($buckets, $portfolio) => array_merge(
            [$portfolio],
            collect($bucketLabels)->keys()->map(fn ($key) => $buckets[$key]['amount'] > 0 ? number_format($buckets[$key]['amount']) : '—')->all(),
            [collect($bucketLabels)->keys()->sum(fn ($key) => $buckets[$key]['amount']) > 0
                ? number_format(collect($bucketLabels)->keys()->sum(fn ($key) => $buckets[$key]['amount']))
                : '—']
        ))
        ->values();

    $branchNetworkRows = Branch::with(['province', 'district'])
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

    return view('public-dashboard', [
        'fiscalYears' => $fiscalYears,
        'months' => $months,
        'provinces' => $provinces,
        'districtsByProvince' => $provinces
            ->mapWithKeys(fn ($province) => [
                $province->province_name => $province->districts->pluck('district_name')->values(),
            ])
            ->all(),
        'outstandingClaimCounts' => $outstandingClaimCounts,
        'outstandingClaimAmounts' => $outstandingClaimAmounts,
        'branchNetworkRows' => $branchNetworkRows,
        'totalProvinceCount' => $provinces->count(),
    ]);
};

Route::get('/', $publicDashboard)->name('public.view');
Route::get('/public-view', $publicDashboard)->name('public.view.direct');

Route::get('/dashboard', function () {
    $monthNames = [
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
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/upload', [ImportLogController::class, 'create'])->name('upload.create');
    Route::post('/upload', [ImportLogController::class, 'store'])->name('upload.store');
    Route::get('/import-data', [ImportLogController::class, 'importModule'])->name('upload.import-module');
    Route::post('/import-data', [ImportLogController::class, 'importFromModule'])->name('upload.import-module.store');
    Route::delete('/import-data/{importLog}', [ImportLogController::class, 'destroyImportedData'])->name('upload.import-module.destroy');
    Route::post('/upload/{importLog}/import', [ImportLogController::class, 'import'])->name('upload.import');
    Route::get('/database-import-history', [ImportLogController::class, 'databaseHistory'])->name('upload.database-history');

    Route::get('/master-data', function () {
        $monthNames = [
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
        $fiscalYears = ImportLog::whereNotNull('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');

        if ($fiscalYears->isEmpty()) {
            $fiscalYears = collect(['2082-83', '2081-82', '2080-81', '2079-80', '2078-79']);
        }

        $provinces = Province::withCount(['districts', 'branches'])->orderBy('province_id')->get();
        $districts = District::with('province')->withCount('branches')->orderBy('district_id')->get();
        $policies = Policy::orderBy('policy_id')->get();
        $branches = Branch::with(['province', 'district'])->orderBy('branch_code')->get();
        $complainTypes = DB::table('complain_types')->orderBy('id')->get();

        // For modal dropdowns
        $allProvinces = Province::orderBy('province_name')->get(['province_id', 'province_name']);
        $allDistricts = District::with('province')->orderBy('district_name')->get(['district_id', 'province_id', 'district_name']);
        $parentPolicies = Policy::whereNull('parent_id')->orderBy('policy_name')->get(['policy_id', 'policy_name']);

        return view('master-data.index', compact('provinces', 'districts', 'policies', 'branches', 'complainTypes', 'allProvinces', 'allDistricts', 'parentPolicies', 'fiscalYears', 'monthNames'));
    })->name('master-data.index');

    Route::resource('users', UserController::class)->except(['show', 'create', 'store']);
    Route::resource('provinces', ProvinceController::class)->except(['show']);
    Route::resource('districts', DistrictController::class)->except(['show']);
    Route::resource('policies', PolicyController::class)->except(['show']);
    Route::resource('branches', BranchController::class)->except(['show']);
    Route::resource('complains', ComplainController::class)->except(['show']);
    Route::resource('import-logs', ImportLogController::class)->except(['show', 'create', 'store']);
});

require __DIR__.'/auth.php';
