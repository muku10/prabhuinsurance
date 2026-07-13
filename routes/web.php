<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportLogController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\FinancialHighlightController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicDashboardController::class)->name('public.view');
Route::get('/public-view', PublicDashboardController::class)->name('public.view.direct');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::get('/financial-highlights/upload', [FinancialHighlightController::class, 'create'])->name('financial-highlights.upload');
    Route::post('/financial-highlights/upload', [FinancialHighlightController::class, 'store'])->name('financial-highlights.store');
    Route::get('/financial-highlights/import', [FinancialHighlightController::class, 'importPage'])->name('financial-highlights.import');
    Route::post('/financial-highlights/import', [FinancialHighlightController::class, 'import'])->name('financial-highlights.import.store');
    Route::get('/financial-highlights/history', [FinancialHighlightController::class, 'history'])->name('financial-highlights.history');
    Route::get('/financial-highlights/template', [FinancialHighlightController::class, 'template'])->name('financial-highlights.template');
    Route::delete('/financial-highlights/{financialHighlightImport}', [FinancialHighlightController::class, 'destroy'])->name('financial-highlights.destroy');

    Route::get('/master-data', MasterDataController::class)->name('master-data.index');

    Route::resource('users', UserController::class)->except(['show', 'create', 'store']);
    Route::resource('provinces', ProvinceController::class)->except(['show']);
    Route::resource('districts', DistrictController::class)->except(['show']);
    Route::resource('policies', PolicyController::class)->except(['show']);
    Route::resource('branches', BranchController::class)->except(['show']);
    Route::resource('complains', ComplainController::class)->except(['show']);
    Route::resource('import-logs', ImportLogController::class)->except(['show', 'create', 'store']);
});

require __DIR__.'/auth.php';
