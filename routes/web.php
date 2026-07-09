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
use App\Models\Policy;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

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
        $provinces = Province::withCount(['districts', 'branches'])->orderBy('province_id')->get();
        $districts = District::with('province')->withCount('branches')->orderBy('district_id')->get();
        $policies = Policy::orderBy('policy_id')->get();
        $branches = Branch::with(['province', 'district'])->orderBy('branch_code')->get();
        $complainTypes = DB::table('complain_types')->orderBy('id')->get();

        // For modal dropdowns
        $allProvinces = Province::orderBy('province_name')->get(['province_id', 'province_name']);
        $allDistricts = District::with('province')->orderBy('district_name')->get(['district_id', 'province_id', 'district_name']);
        $parentPolicies = Policy::whereNull('parent_id')->orderBy('policy_name')->get(['policy_id', 'policy_name']);

        return view('master-data.index', compact('provinces', 'districts', 'policies', 'branches', 'complainTypes', 'allProvinces', 'allDistricts', 'parentPolicies'));
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
