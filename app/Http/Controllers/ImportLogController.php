<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Complain;
use App\Models\District;
use App\Models\ImportLog;
use App\Models\IntimationClaim;
use App\Models\OutstandingClaim;
use App\Models\PaidClaim;
use App\Models\Premium;
use App\Models\Policy;
use App\Models\Province;
use App\Models\Transaction;
use App\Models\WithdrawalClaim;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportLogController extends Controller
{
    private const SHEET_TRANSACTIONS = 0;
    private const SHEET_COMPLAINS = 1;
    private const SHEET_BRANCH_NETWORK = 2;
    private const IMPORTABLE_UPLOAD_TYPES = ['irms'];

    private const UPLOAD_FIELD_MAP = [
        'irms' => 'irms_file',
        'outstanding_claim' => 'outstanding_claim_file',
        'paid_claim' => 'paid_claim_file',
        'withdrawal_claim' => 'withdrawal_claim_file',
        'intimation_claim' => 'intimation_claim_file',
    ];

    private const UPLOAD_TYPE_META = [
        'irms' => [
            'title' => 'IRMS',
            'description' => 'Upload the core IRMS workbook for the selected period.',
            'extensions' => 'xlsx,xls,csv',
            'accept' => '.xlsx,.xls,.csv',
        ],
        'outstanding_claim' => [
            'title' => 'Outstanding Claim',
            'description' => 'Attach the outstanding claim file for the selected period.',
            'extensions' => 'xlsx,xls,csv,pdf',
            'accept' => '.xlsx,.xls,.csv,.pdf',
        ],
        'paid_claim' => [
            'title' => 'Paid Claim',
            'description' => 'Attach the paid claim file for the selected period.',
            'extensions' => 'xlsx,xls,csv,pdf',
            'accept' => '.xlsx,.xls,.csv,.pdf',
        ],
        'withdrawal_claim' => [
            'title' => 'Withdrawal Claim',
            'description' => 'Attach the withdrawal claim file for the selected period.',
            'extensions' => 'xlsx,xls,csv,pdf',
            'accept' => '.xlsx,.xls,.csv,.pdf',
        ],
        'intimation_claim' => [
            'title' => 'Intimation Claim',
            'description' => 'Attach the intimation claim file for the selected period.',
            'extensions' => 'xlsx,xls,csv,pdf',
            'accept' => '.xlsx,.xls,.csv,.pdf',
        ],
    ];

    public function index(Request $request): View
    {
        $monthNames = $this->bsMonthNames();
        $fiscalYears = ImportLog::select('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $selectedFiscalYear = $request->string('fiscal_year')->toString();
        $selectedMonth = $request->integer('month') ?: null;

        $importLogs = ImportLog::with('user')
            ->when($selectedFiscalYear !== '', fn ($query) => $query->where('fiscal_year', $selectedFiscalYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->latest('date')
            ->latest('id')
            ->get();

        return view('import-logs.index', compact('importLogs', 'monthNames', 'fiscalYears', 'selectedFiscalYear', 'selectedMonth'));
    }

    public function databaseHistory(Request $request): View
    {
        $monthNames = $this->bsMonthNames();
        $fiscalYears = ImportLog::where('status', 'completed')->select('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $selectedFiscalYear = $request->string('fiscal_year')->toString();
        $selectedMonth = $request->integer('month') ?: null;

        $importHistory = ImportLog::with(['user', 'premiums', 'intimationClaims', 'paidClaims', 'withdrawalClaims', 'outstandingClaims'])
            ->withCount(['premiums', 'intimationClaims', 'paidClaims', 'withdrawalClaims', 'outstandingClaims'])
            ->where('status', 'completed')
            ->when($selectedFiscalYear !== '', fn ($query) => $query->where('fiscal_year', $selectedFiscalYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->latest('date')
            ->latest('id')
            ->get();

        return view('import-logs.database-history', compact('importHistory', 'monthNames', 'fiscalYears', 'selectedFiscalYear', 'selectedMonth'));
    }

    public function create(): View
    {
        $recentUploads = ImportLog::latest('date')->latest('id')->limit(8)->get();
        $today = now();
        $currentPeriod = $this->currentBsPeriod($today);
        $monthNames = $this->bsMonthNames();
        $submissionDateBs = $this->currentBsDate($today, $monthNames);
        $fiscalYearOptions = $this->fiscalYearOptions($currentPeriod['fiscal_year']);
        $selectedImportLogId = session('selected_import_log_id');
        $selectedImportLog = $selectedImportLogId ? ImportLog::find($selectedImportLogId) : $recentUploads->first();

        return view('import-logs.create', compact(
            'recentUploads',
            'currentPeriod',
            'monthNames',
            'submissionDateBs',
            'fiscalYearOptions',
            'selectedImportLog'
        ));
    }

    public function importModule(): View
    {
        $monthNames = $this->bsMonthNames();
        $availableImportLogs = ImportLog::where('status', '!=', 'completed')
            ->latest('date')
            ->latest('id')
            ->get();

        $selectedImportLogId = request()->integer('import_log_id') ?: session('selected_import_log_id');
        $selectedImportLog = $selectedImportLogId
            ? $availableImportLogs->firstWhere('id', $selectedImportLogId)
            : $availableImportLogs->first();

        return view('import-logs.import', compact('availableImportLogs', 'monthNames', 'selectedImportLog'));
    }

    public function store(Request $request): RedirectResponse
    {
        $today = now();

        $request->validate([
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'irms_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'outstanding_claim_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:20480'],
            'paid_claim_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:20480'],
            'withdrawal_claim_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:20480'],
            'intimation_claim_file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:20480'],
        ]);

        $createdImportLogs = [];
        $fiscalYearFolder = str_replace(['/', '\\'], '-', $request->string('fiscal_year')->toString());
        $monthFolder = str_pad((string) $request->integer('month'), 2, '0', STR_PAD_LEFT);

        foreach (self::UPLOAD_FIELD_MAP as $uploadType => $fieldName) {
            if (! $request->hasFile($fieldName)) {
                continue;
            }

            $file = $request->file($fieldName);
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $baseName);
            $fileName = now()->format('YmdHis').'-'.$uploadType.'-'.$safeName.'.'.$extension;
            $storagePath = 'uploads/'.$uploadType.'/'.$fiscalYearFolder.'/'.$monthFolder;
            $storedFile = $file->storeAs($storagePath, $fileName, 'public');

            $createdImportLogs[] = ImportLog::create([
                'date' => $today->toDateString(),
                'user_id' => auth()->id(),
                'upload_type' => $uploadType,
                'file_name' => $storedFile,
                'fiscal_year' => $request->string('fiscal_year')->toString(),
                'month' => $request->integer('month'),
                'status' => 'pending',
            ]);
        }

        if ($createdImportLogs === []) {
            return back()->withErrors([
                'upload_files' => 'Choose at least one file to upload.',
            ])->withInput();
        }

        $selectedImportLog = collect($createdImportLogs)->firstWhere('upload_type', 'irms') ?? $createdImportLogs[0];

        return redirect()->route('upload.type', $selectedImportLog->upload_type ?: 'irms')
            ->with('selected_import_log_id', $selectedImportLog->id)
            ->with('toast', [
                'message' => count($createdImportLogs).' file(s) uploaded successfully. Use the IRMS file for database import.',
                'type' => 'success',
            ]);
    }

    public function createForType(string $type): View
    {
        abort_unless(array_key_exists($type, self::UPLOAD_TYPE_META), 404);

        $meta = self::UPLOAD_TYPE_META[$type];
        $today = now();
        $currentPeriod = $this->currentBsPeriod($today);
        $monthNames = $this->bsMonthNames();
        $submissionDateBs = $this->currentBsDate($today, $monthNames);
        $fiscalYearOptions = $this->fiscalYearOptions($currentPeriod['fiscal_year']);
        $recentUploads = ImportLog::where('upload_type', $type)
            ->latest('date')
            ->latest('id')
            ->limit(8)
            ->get();
        $selectedImportLogId = session('selected_import_log_id');
        $selectedImportLog = $selectedImportLogId ? ImportLog::find($selectedImportLogId) : $recentUploads->first();

        return view('import-logs.upload-type', compact(
            'type',
            'meta',
            'recentUploads',
            'currentPeriod',
            'monthNames',
            'submissionDateBs',
            'fiscalYearOptions',
            'selectedImportLog'
        ));
    }

    public function storeForType(Request $request, string $type): RedirectResponse
    {
        abort_unless(array_key_exists($type, self::UPLOAD_TYPE_META), 404);

        $meta = self::UPLOAD_TYPE_META[$type];
        $fieldName = self::UPLOAD_FIELD_MAP[$type];

        $validated = $request->validate([
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            $fieldName => ['required', 'file', 'mimes:'.$meta['extensions'], 'max:20480'],
        ]);

        $today = now();
        $file = $request->file($fieldName);
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $baseName);
        $fileName = $today->format('YmdHis').'-'.$type.'-'.$safeName.'.'.$extension;
        $fiscalYearFolder = str_replace(['/', '\\'], '-', $validated['fiscal_year']);
        $monthFolder = str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT);
        $storagePath = 'uploads/'.$type.'/'.$fiscalYearFolder.'/'.$monthFolder;
        $storedFile = $file->storeAs($storagePath, $fileName, 'public');

        $importLog = ImportLog::create([
            'date' => $today->toDateString(),
            'user_id' => auth()->id(),
            'upload_type' => $type,
            'file_name' => $storedFile,
            'fiscal_year' => $validated['fiscal_year'],
            'month' => $validated['month'],
            'status' => 'pending',
        ]);

        return redirect()->route('upload.type', $type)
            ->with('selected_import_log_id', $importLog->id)
            ->with('toast', [
                'message' => $meta['title'].' file uploaded successfully.',
                'type' => 'success',
            ]);
    }

    public function import(ImportLog $importLog): RedirectResponse
    {
        $redirectType = $importLog->upload_type ?: 'irms';

        return $this->performImport($importLog, 'upload.type', ['type' => $redirectType]);
    }

    public function importFromModule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'import_log_id' => ['required', 'integer', 'exists:import_logs,id'],
        ]);

        $importLog = $this->importableImportLogQuery()->findOrFail($validated['import_log_id']);

        return $this->performImport($importLog, 'upload.import-module');
    }

    public function edit(ImportLog $importLog): View
    {
        return view('import-logs.edit', compact('importLog'));
    }

    public function update(Request $request, ImportLog $importLog): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'status' => ['required', 'in:pending,processing,completed,failed'],
            'upload_type' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:20480'],
        ]);

        unset($validated['file']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $baseName);
            $fileName = now()->format('YmdHis').'-'.$safeName.'.'.$extension;
            $fiscalYearFolder = str_replace(['/', '\\'], '-', $validated['fiscal_year']);
            $monthFolder = str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT);
            $storagePath = 'uploads/'.($validated['upload_type'] ?? 'legacy').'/'.$fiscalYearFolder.'/'.$monthFolder;

            $validated['file_name'] = $file->storeAs($storagePath, $fileName, 'public');

            if ($importLog->file_name) {
                Storage::disk('public')->delete($importLog->file_name);
            }
        }

        $importLog->update($validated);

        return redirect()->route('import-logs.index')->with('toast', [
            'message' => 'Import log updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(ImportLog $importLog): RedirectResponse
    {
        if ($importLog->file_name) {
            Storage::disk('public')->delete($importLog->file_name);
        }

        $importLog->delete();

        return redirect()->route('import-logs.index')->with('toast', [
            'message' => 'Import log and uploaded file deleted successfully.',
            'type' => 'success',
        ]);
    }

    public function destroyImportedData(ImportLog $importLog): RedirectResponse
    {
        $totalDeleted = $this->deleteImportedRows($importLog);
        $importLog->update(['status' => 'pending']);

        return redirect()->route('upload.import-module')->with('toast', [
            'message' => $totalDeleted > 0
                ? 'Imported data deleted. The uploaded file is still available for re-import.'
                : 'No imported database rows were found for this file.',
            'type' => $totalDeleted > 0 ? 'success' : 'warning',
        ]);
    }

    private function performImport(ImportLog $importLog, string $redirectRoute, array $routeParams = []): RedirectResponse
    {
        if ($importLog->status === 'completed') {
            return redirect()->route($redirectRoute, $routeParams)
                ->with('selected_import_log_id', $importLog->id)
                ->with('toast', [
                    'message' => 'This file has already been imported.',
                    'type' => 'success',
                ]);
        }

        try {
            DB::transaction(function () use ($importLog) {
                $importLog->update(['status' => 'processing']);

                $filePath = Storage::disk('public')->path($importLog->file_name);
                $spreadsheet = IOFactory::load($filePath);

                $uploadType = $this->normalizeUploadType($importLog->upload_type);
                [$modelClass, $rows] = $this->buildRowsForUploadType($spreadsheet, $importLog, $uploadType);

                $modelClass::where('import_log_id', $importLog->id)->delete();

                if (empty($rows)) {
                    throw new \RuntimeException('No valid data rows were found in the uploaded file.');
                }

                $modelClass::insert($rows);

                $importLog->update(['status' => 'completed']);
            });
        } catch (\Throwable $exception) {
            $importLog->update(['status' => 'failed']);

            return redirect()->route($redirectRoute, $routeParams)
                ->with('selected_import_log_id', $importLog->id)
                ->withErrors([
                    'file' => 'Import failed: '.$exception->getMessage(),
                ]);
        }

        return redirect()->route($redirectRoute, $routeParams)
            ->with('selected_import_log_id', $importLog->id)
            ->with('toast', [
                'message' => 'Data imported into the database successfully.',
                'type' => 'success',
            ]);
    }

    private function uploadTypes(): array
    {
        return self::UPLOAD_FIELD_MAP;
    }

    private function importableImportLogQuery()
    {
        return ImportLog::query();
    }

    private function buildPremiumRowsFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows, [
            'stateid',
            'districtid',
            'month',
            'department',
            'class',
            'freshpolicy',
            'renewalpolicy',
            'endrosementpolicy',
            'grosspremiumincome',
            'suminsured',
            'remarks',
        ]);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded premium file is missing required headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $records = [];
        $timestamp = now();
        $validProvinceIds = Province::query()->pluck('province_id')->flip();
        $validDistrictIds = District::query()->pluck('district_id')->flip();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = $this->mapHeaderRow($headers, $row);

            if (! $this->hasAnyValue($mappedRow, ['stateid', 'districtid', 'department', 'class', 'freshpolicy', 'renewalpolicy', 'endrosementpolicy', 'grosspremiumincome', 'suminsured', 'remarks'])) {
                continue;
            }

            $records[] = [
                'import_log_id' => $importLog->id,
                'state_id' => $this->sanitizeForeignKey($mappedRow['stateid'] ?? null, $validProvinceIds),
                'district_id' => $this->sanitizeForeignKey($mappedRow['districtid'] ?? null, $validDistrictIds),
                'fiscal_year' => $importLog->fiscal_year,
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? (int) $importLog->month,
                'department' => $this->nullableString($mappedRow['department'] ?? null),
                'class' => $this->nullableString($mappedRow['class'] ?? null),
                'fresh_policy' => $this->numericInteger($mappedRow['freshpolicy'] ?? null),
                'renewal_policy' => $this->numericInteger($mappedRow['renewalpolicy'] ?? null),
                'endrosement_policy' => $this->numericInteger($mappedRow['endrosementpolicy'] ?? null),
                'gross_premium_income' => $this->numericDecimal($mappedRow['grosspremiumincome'] ?? null),
                'sum_insured' => $this->numericDecimal($mappedRow['suminsured'] ?? null),
                'remarks' => $this->nullableString($mappedRow['remarks'] ?? null),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function buildIntimationClaimRowsFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows, [
            'province',
            'district',
            'branch',
            'department',
            'month',
            'class',
            'estimatedloss',
            'status',
        ]);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded intimation file is missing required headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $records = [];
        $timestamp = now();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = $this->mapHeaderRow($headers, $row);

            if (! $this->hasAnyValue($mappedRow, ['province', 'district', 'branch', 'department', 'class', 'estimatedloss', 'status'])) {
                continue;
            }

            $records[] = [
                'import_log_id' => $importLog->id,
                'fiscal_year' => $importLog->fiscal_year,
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? (int) $importLog->month,
                'province' => $this->nullableString($mappedRow['province'] ?? null),
                'district' => $this->nullableString($mappedRow['district'] ?? null),
                'branch' => $this->nullableString($mappedRow['branch'] ?? null),
                'department' => $this->nullableString($mappedRow['department'] ?? null),
                'class' => $this->nullableString($mappedRow['class'] ?? null),
                'estimated_loss' => $this->nullableDecimal($mappedRow['estimatedloss'] ?? null) ?? 0,
                'status' => $this->nullableString($mappedRow['status'] ?? null),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function buildPaidClaimRowsFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows, [
            'month',
            'department',
            'class',
            'province',
            'district',
            'branchname',
            'totalpaidamount',
            'turnarounddays',
        ]);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded paid claim file is missing required headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $records = [];
        $timestamp = now();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = $this->mapHeaderRow($headers, $row);

            if (! $this->hasAnyValue($mappedRow, ['month', 'department', 'class', 'province', 'district', 'branchname', 'totalpaidamount', 'turnarounddays'])) {
                continue;
            }

            $records[] = [
                'import_log_id' => $importLog->id,
                'fiscal_year' => $importLog->fiscal_year,
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? (int) $importLog->month,
                'department' => $this->nullableString($mappedRow['department'] ?? null),
                'class' => $this->nullableString($mappedRow['class'] ?? null),
                'province' => $this->nullableString($mappedRow['province'] ?? null),
                'district' => $this->nullableString($mappedRow['district'] ?? null),
                'branch_name' => $this->nullableString($mappedRow['branchname'] ?? null),
                'total_paid_amount' => $this->nullableDecimal($mappedRow['totalpaidamount'] ?? null) ?? 0,
                'turnaround_days' => $this->nullableInteger($mappedRow['turnarounddays'] ?? null) ?? 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function buildWithdrawalClaimRowsFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows, [
            'month',
            'province',
            'district',
            'branch',
            'department',
            'class',
        ]);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded withdrawal claim file is missing required headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $records = [];
        $timestamp = now();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = $this->mapHeaderRow($headers, $row);

            if (! $this->hasAnyValue($mappedRow, ['month', 'province', 'district', 'branch', 'department', 'class'])) {
                continue;
            }

            $records[] = [
                'import_log_id' => $importLog->id,
                'fiscal_year' => $importLog->fiscal_year,
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? (int) $importLog->month,
                'province' => $this->nullableString($mappedRow['province'] ?? null),
                'district' => $this->nullableString($mappedRow['district'] ?? null),
                'branch' => $this->nullableString($mappedRow['branch'] ?? null),
                'department' => $this->nullableString($mappedRow['department'] ?? null),
                'class' => $this->nullableString($mappedRow['class'] ?? null),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function buildOutstandingClaimRowsFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows, [
            'province',
            'district',
            'branch',
            'department',
            'class',
            'amount',
            'developmentyear',
        ]);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded outstanding claim file is missing required headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $records = [];
        $timestamp = now();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = $this->mapHeaderRow($headers, $row);

            if (! $this->hasAnyValue($mappedRow, ['province', 'district', 'branch', 'department', 'class', 'amount', 'developmentyear'])) {
                continue;
            }

            $records[] = [
                'import_log_id' => $importLog->id,
                'fiscal_year' => $importLog->fiscal_year,
                'development_year' => $this->nullableString($mappedRow['developmentyear'] ?? null),
                'province' => $this->nullableString($mappedRow['province'] ?? null),
                'district' => $this->nullableString($mappedRow['district'] ?? null),
                'branch' => $this->nullableString($mappedRow['branch'] ?? null),
                'department' => $this->nullableString($mappedRow['department'] ?? null),
                'class' => $this->nullableString($mappedRow['class'] ?? null),
                'amount' => $this->nullableDecimal($mappedRow['amount'] ?? null) ?? 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function findHeaderRowIndex(array $rows, array $requiredHeaders): ?int
    {
        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn ($value) => $this->normalizeHeader((string) $value), $row);

            if (empty(array_diff($requiredHeaders, $normalizedRow))) {
                return $index;
            }
        }

        return null;
    }

    private function mapHeaderRow(array $headers, array $row): array
    {
        $mappedRow = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $mappedRow[$header] = $row[$index] ?? null;
        }

        return $mappedRow;
    }

    private function hasAnyValue(array $mappedRow, array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->nullableString($mappedRow[$key] ?? null) !== null) {
                return true;
            }

            if ($this->nullableInteger($mappedRow[$key] ?? null) !== null) {
                return true;
            }
        }

        return false;
    }

    private function deleteImportedRows(ImportLog $importLog): int
    {
        return match ($this->normalizeUploadType($importLog->upload_type)) {
            'premium' => Premium::where('import_log_id', $importLog->id)->delete(),
            'intimation_claim' => IntimationClaim::where('import_log_id', $importLog->id)->delete(),
            'paid_claim' => PaidClaim::where('import_log_id', $importLog->id)->delete(),
            'withdrawal_claim' => WithdrawalClaim::where('import_log_id', $importLog->id)->delete(),
            'outstanding_claim' => OutstandingClaim::where('import_log_id', $importLog->id)->delete(),
            default => 0,
        };
    }

    private function normalizeUploadType(?string $uploadType): string
    {
        $normalized = $uploadType ? strtolower(trim($uploadType)) : 'premium';

        return $normalized === 'irms' ? 'premium' : $normalized;
    }

    private function buildRowsForUploadType($spreadsheet, ImportLog $importLog, string $uploadType): array
    {
        return match ($uploadType) {
            'premium' => [Premium::class, $this->buildPremiumRowsFromUpload($spreadsheet, $importLog)],
            'intimation_claim' => [IntimationClaim::class, $this->buildIntimationClaimRowsFromUpload($spreadsheet, $importLog)],
            'paid_claim' => [PaidClaim::class, $this->buildPaidClaimRowsFromUpload($spreadsheet, $importLog)],
            'withdrawal_claim' => [WithdrawalClaim::class, $this->buildWithdrawalClaimRowsFromUpload($spreadsheet, $importLog)],
            'outstanding_claim' => [OutstandingClaim::class, $this->buildOutstandingClaimRowsFromUpload($spreadsheet, $importLog)],
            default => throw new \RuntimeException('Unsupported upload type: '.$uploadType),
        };
    }

    private function buildTransactionsFromUpload($spreadsheet, ImportLog $importLog, string $fiscalYear, int $selectedMonth, string $importBatchToken): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findTransactionHeaderRowIndex($rows);

        if ($headerIndex === null) {
            throw new \RuntimeException('The uploaded file is missing the required transaction headers.');
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $transactions = [];
        $timestamp = now();
        $validProvinceIds = Province::query()->pluck('province_id')->flip();
        $validDistrictIds = District::query()->pluck('district_id')->flip();
        $validPolicyIds = Policy::query()->pluck('policy_id')->flip();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = [];

            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }

                $mappedRow[$header] = $row[$index] ?? null;
            }

            if (! $this->isTransactionRow($mappedRow)) {
                continue;
            }

            $stateId = $this->sanitizeForeignKey($mappedRow['stateid'] ?? null, $validProvinceIds);
            $districtId = $this->sanitizeForeignKey($mappedRow['districtid'] ?? null, $validDistrictIds);
            $policyId = $this->sanitizeForeignKey($mappedRow['staticpoliciesid'] ?? null, $validPolicyIds);
            $subPolicyId = $this->sanitizeForeignKey($mappedRow['staticsubpoliciesid'] ?? null, $validPolicyIds);

            $transactions[] = [
                'import_log_id' => $importLog->id,
                'import_batch_token' => $importBatchToken,
                'state_id' => $stateId,
                'district_id' => $districtId,
                'static_policies_id' => $policyId,
                'static_sub_policies_id' => $subPolicyId,
                'fiscal_year' => $fiscalYear,
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? $selectedMonth,
                'number_of_issued_policy' => $this->numericInteger($mappedRow['numberofissuedpolicy'] ?? null),
                'as_on_issued_policy' => $this->numericInteger($mappedRow['asonissuedpolicy'] ?? null),
                'gross_premium_income' => $this->numericDecimal($mappedRow['grosspremiumincome'] ?? null),
                'sum_insured' => $this->numericDecimal($mappedRow['suminsured'] ?? null),
                'number_of_gross_claim' => $this->numericInteger($mappedRow['numberofgrossclaim'] ?? null),
                'amount_of_gross_claim' => $this->numericDecimal($mappedRow['amountofgrossclaim'] ?? null),
                'number_of_gross_claim_paid' => $this->numericInteger($mappedRow['numberofgrossclaimpaid'] ?? null),
                'amount_of_gross_claim_paid' => $this->numericDecimal($mappedRow['amountofgrossclaimpaid'] ?? null),
                'number_of_outstanding_claim' => $this->numericInteger($mappedRow['numberofoutstandingclaim'] ?? null),
                'amount_of_outstanding_claim' => $this->numericDecimal($mappedRow['amountofoutstandingclaim'] ?? null),
                'remarks' => $this->nullableString($mappedRow['remarks'] ?? null),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $transactions;
    }

    private function findTransactionHeaderRowIndex(array $rows): ?int
    {
        $requiredHeaders = [
            'stateid',
            'districtid',
            'month',
            'staticpoliciesid',
            'staticsubpoliciesid',
            'numberofissuedpolicy',
            'asonissuedpolicy',
            'grosspremiumincome',
            'suminsured',
            'numberofgrossclaim',
            'amountofgrossclaim',
            'numberofgrossclaimpaid',
            'amountofgrossclaimpaid',
            'numberofoutstandingclaim',
            'amountofoutstandingclaim',
            'remarks',
        ];

        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn ($value) => $this->normalizeHeader((string) $value), $row);

            if (empty(array_diff($requiredHeaders, $normalizedRow))) {
                return $index;
            }
        }

        return null;
    }

    private function isTransactionRow(array $mappedRow): bool
    {
        $requiredIds = [
            $this->nullableInteger($mappedRow['stateid'] ?? null),
            $this->nullableInteger($mappedRow['districtid'] ?? null),
            $this->nullableInteger($mappedRow['staticpoliciesid'] ?? null),
        ];

        return count(array_filter($requiredIds, fn ($value) => $value !== null)) === count($requiredIds);
    }

    private function buildComplainsFromUpload($spreadsheet, ImportLog $importLog, string $fiscalYear, int $selectedMonth): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findComplainHeaderRowIndex($rows);

        if ($headerIndex === null) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $complains = [];
        $timestamp = now();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = [];

            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }

                $mappedRow[$header] = $row[$index] ?? null;
            }

            $complainType = $this->nullableString($mappedRow['complaint_type'] ?? $mappedRow['complain_type'] ?? $mappedRow['complaintype'] ?? null);

            if ($complainType === null) {
                continue;
            }

            $receivedNum = $this->numericInteger($mappedRow['received_num'] ?? $mappedRow['receivednum'] ?? null);
            $resolvedNum = $this->numericInteger($mappedRow['resolved_num'] ?? $mappedRow['resolvednum'] ?? null);
            $pendingNum = $this->numericInteger($mappedRow['pending_num'] ?? $mappedRow['pendingnum'] ?? null);

            if ($receivedNum === 0 && $resolvedNum === 0 && $pendingNum === 0) {
                continue;
            }

            $complains[] = [
                'import_log_id' => $importLog->id,
                'year' => $this->nullableInteger($mappedRow['year'] ?? null) ?? (int) substr($fiscalYear, 0, 4),
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? $selectedMonth,
                'complain_type' => $complainType,
                'received_num' => $receivedNum,
                'resolved_num' => $resolvedNum,
                'pending_num' => $pendingNum,
                'average_resolution_time' => $this->nullableDecimal($mappedRow['average_resolution_time'] ?? $mappedRow['averageresolutiontime'] ?? null),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $complains;
    }

    private function findComplainHeaderRowIndex(array $rows): ?int
    {
        $requiredHeaders = [
            'complaintype',
            'receivednum',
            'resolvednum',
            'pendingnum',
        ];

        // Also accept alternate header names
        $alternateHeaders = [
            'complaint_type',
            'received_num',
            'resolved_num',
            'pending_num',
        ];

        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn ($value) => $this->normalizeHeader((string) $value), $row);

            if (empty(array_diff($requiredHeaders, $normalizedRow)) || empty(array_diff($alternateHeaders, $normalizedRow))) {
                return $index;
            }
        }

        return null;
    }

    private function buildBranchesFromUpload($spreadsheet, ImportLog $importLog): array
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findBranchHeaderRowIndex($rows);

        if ($headerIndex === null) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), $rows[$headerIndex]);
        $branches = [];
        $timestamp = now();
        $validProvinceIds = Province::query()->pluck('province_id')->flip();
        $validDistrictIds = District::query()->pluck('district_id')->flip();

        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mappedRow = [];

            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }

                $mappedRow[$header] = $row[$index] ?? null;
            }

            $provinceId = $this->sanitizeForeignKey($mappedRow['province_id'] ?? $mappedRow['provinceid'] ?? null, $validProvinceIds);
            $districtId = $this->sanitizeForeignKey($mappedRow['district_id'] ?? $mappedRow['districtid'] ?? null, $validDistrictIds);

            // Skip rows without valid province/district
            if ($provinceId === null && $districtId === null) {
                continue;
            }

            $numberOfBranch = $this->numericInteger($mappedRow['number_of_branch'] ?? $mappedRow['numberofbranch'] ?? null);
            $numberOfAgents = $this->numericInteger($mappedRow['number_of_agents'] ?? $mappedRow['numberofagents'] ?? null);
            $numberOfSurveyors = $this->numericInteger($mappedRow['number_of_surveyors'] ?? $mappedRow['numberofsurveyors'] ?? null);

            if ($numberOfBranch === 0 && $numberOfAgents === 0 && $numberOfSurveyors === 0) {
                continue;
            }

            $branches[] = [
                'import_log_id' => $importLog->id,
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'fiscal_year' => $this->nullableString($mappedRow['fiscal_year'] ?? $mappedRow['fiscalyear'] ?? null) ?? $importLog->fiscal_year,
                'year' => $this->nullableInteger($mappedRow['year'] ?? null) ?? (int) substr($importLog->fiscal_year, 0, 4),
                'month' => $this->nullableInteger($mappedRow['month'] ?? null) ?? (int) $importLog->month,
                'number_of_branch' => $numberOfBranch,
                'number_of_agents' => $numberOfAgents,
                'number_of_surveyors' => $numberOfSurveyors,
                'status' => 'active',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $branches;
    }

    private function findBranchHeaderRowIndex(array $rows): ?int
    {
        $requiredHeaders = [
            'provinceid',
            'districtid',
            'numberofbranch',
            'numberofagents',
            'numberofsurveyors',
        ];

        $alternateHeaders = [
            'province_id',
            'district_id',
            'number_of_branch',
            'number_of_agents',
            'number_of_surveyors',
        ];

        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn ($value) => $this->normalizeHeader((string) $value), $row);

            if (empty(array_diff($requiredHeaders, $normalizedRow)) || empty(array_diff($alternateHeaders, $normalizedRow))) {
                return $index;
            }
        }

        return null;
    }

    private function normalizeHeader(string $header): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', trim($header)) ?? '');
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return (int) round((float) str_replace(',', '', (string) $value));
    }

    private function numericInteger(mixed $value): int
    {
        return $this->nullableInteger($value) ?? 0;
    }

    private function numericDecimal(mixed $value): float
    {
        if ($value === null || trim((string) $value) === '') {
            return 0;
        }

        return round((float) str_replace(',', '', (string) $value), 2);
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return round((float) str_replace(',', '', (string) $value), 2);
    }

    private function sanitizeForeignKey(mixed $value, \Illuminate\Support\Collection $validIds): ?int
    {
        $normalized = $this->nullableInteger($value);

        if ($normalized === null) {
            return null;
        }

        return $validIds->has($normalized) ? $normalized : null;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function currentBsPeriod(CarbonInterface $date): array
    {
        $year = (int) $date->format('Y');
        $monthDay = $date->format('m-d');

        $bsYear = $year + ($monthDay >= '04-14' ? 57 : 56);
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

    private function currentBsDate(CarbonInterface $date, array $monthNames): string
    {
        $period = $this->currentBsPeriod($date);
        $monthBoundaries = [
            1 => '04-14',
            2 => '05-15',
            3 => '06-15',
            4 => '07-17',
            5 => '08-17',
            6 => '09-17',
            7 => '10-18',
            8 => '11-17',
            9 => '12-16',
            10 => '01-15',
            11 => '02-13',
            12 => '03-15',
        ];

        $boundaryYear = (int) $date->format('Y');
        $startDate = $date->copy()->setDate(
            $boundaryYear,
            (int) substr($monthBoundaries[$period['month']], 0, 2),
            (int) substr($monthBoundaries[$period['month']], 3, 2)
        );

        if ($date->lt($startDate)) {
            $startDate->subYear();
        }

        $day = $startDate->diffInDays($date) + 1;
        $monthName = $monthNames[$period['month']] ?? (string) $period['month'];

        return $period['year'].' '.$monthName.' '.$day;
    }

    private function bsMonthNames(): array
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

    private function fiscalYearOptions(string $currentFiscalYear): array
    {
        [$startYear, $endYearSuffix] = explode('-', $currentFiscalYear);
        $startYear = (int) $startYear;
        $options = [];

        for ($offset = -2; $offset <= 2; $offset++) {
            $year = $startYear + $offset;
            $options[] = $year.'-'.substr((string) ($year + 1), -2);
        }

        return $options;
    }
}
