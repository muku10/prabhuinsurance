<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Complain;
use App\Models\District;
use App\Models\ImportLog;
use App\Models\Policy;
use App\Models\Province;
use App\Models\Transaction;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportLogController extends Controller
{
    private const SHEET_TRANSACTIONS = 0;
    private const SHEET_COMPLAINS = 1;
    private const SHEET_BRANCH_NETWORK = 2;

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

        $importHistory = ImportLog::with(['user', 'transactions', 'complains', 'branches'])
            ->withCount(['transactions', 'complains', 'branches'])
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
        $recentUploads = ImportLog::latest('date')->latest('id')->limit(3)->get();
        $today = now();
        $currentPeriod = $this->currentBsPeriod($today);
        $monthNames = $this->bsMonthNames();
        $submissionDateBs = $this->currentBsDate($today, $monthNames);
        $fiscalYearOptions = $this->fiscalYearOptions($currentPeriod['fiscal_year']);
        $selectedImportLog = ImportLog::find(session('selected_import_log_id'));

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
        $selectedImportLog = $availableImportLogs->firstWhere('id', session('selected_import_log_id')) ?? $availableImportLogs->first();
        return view('import-logs.import', compact('availableImportLogs', 'monthNames', 'selectedImportLog'));
    }

    public function store(Request $request): RedirectResponse
    {
        $today = now();

        $validated = $request->validate([
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '-', $baseName);
        $fileName = now()->format('YmdHis').'-'.$safeName.'.'.$extension;
        $fiscalYearFolder = str_replace(['/', '\\'], '-', $validated['fiscal_year']);
        $monthFolder = str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT);
        $storagePath = 'uploads/'.$fiscalYearFolder.'/'.$monthFolder;
        $storedFile = $file->storeAs($storagePath, $fileName, 'public');

        $importLog = ImportLog::create([
            'date' => $today->toDateString(),
            'user_id' => auth()->id(),
            'file_name' => $storedFile,
            'fiscal_year' => $validated['fiscal_year'],
            'month' => $validated['month'],
            'status' => 'pending',
        ]);

        return redirect()->route('upload.create')
            ->with('selected_import_log_id', $importLog->id)
            ->with('toast', [
                'message' => 'File uploaded successfully. Continue with Step 3 to import it into the database.',
                'type' => 'success',
            ]);
    }

    public function import(ImportLog $importLog): RedirectResponse
    {
        return $this->performImport($importLog, 'upload.create');
    }

    public function importFromModule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'import_log_id' => ['required', 'integer', 'exists:import_logs,id'],
        ]);

        $importLog = ImportLog::findOrFail($validated['import_log_id']);

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
            'file' => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
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
            $storagePath = 'uploads/'.$fiscalYearFolder.'/'.$monthFolder;

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
        $deletedTransactions = 0;
        $deletedComplains = 0;
        $deletedBranches = 0;

        DB::transaction(function () use ($importLog, &$deletedTransactions, &$deletedComplains, &$deletedBranches) {
            $deletedTransactions = Transaction::where('import_log_id', $importLog->id)->delete();
            $deletedComplains = Complain::where('import_log_id', $importLog->id)->delete();
            $deletedBranches = Branch::where('import_log_id', $importLog->id)->delete();
            $importLog->update(['status' => 'pending']);
        });

        $totalDeleted = $deletedTransactions + $deletedComplains + $deletedBranches;

        return redirect()->route('upload.import-module')->with('toast', [
            'message' => $totalDeleted > 0
                ? "Imported data deleted: {$deletedTransactions} transactions, {$deletedComplains} complains, {$deletedBranches} branch network records. The uploaded file is still available for re-import."
                : 'No imported database rows were found for this file.',
            'type' => $totalDeleted > 0 ? 'success' : 'warning',
        ]);
    }

    private function performImport(ImportLog $importLog, string $redirectRoute): RedirectResponse
    {
        if ($importLog->status === 'completed') {
            return redirect()->route($redirectRoute)
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
                $sheetCount = $spreadsheet->getSheetCount();

                $importBatchToken = (string) Str::uuid();
                $totalImported = 0;

                // Sheet 0: Transactions
                Transaction::where('import_log_id', $importLog->id)->delete();
                $spreadsheet->setActiveSheetIndex(self::SHEET_TRANSACTIONS);
                $transactions = $this->buildTransactionsFromUpload($spreadsheet, $importLog, $importLog->fiscal_year, (int) $importLog->month, $importBatchToken);

                if (! empty($transactions)) {
                    Transaction::insert($transactions);
                    $totalImported += count($transactions);
                }

                // Sheet 1: Complains (if present)
                if ($sheetCount > self::SHEET_COMPLAINS) {
                    Complain::where('import_log_id', $importLog->id)->delete();
                    $spreadsheet->setActiveSheetIndex(self::SHEET_COMPLAINS);
                    $complains = $this->buildComplainsFromUpload($spreadsheet, $importLog, $importLog->fiscal_year, (int) $importLog->month);

                    if (! empty($complains)) {
                        Complain::insert($complains);
                        $totalImported += count($complains);
                    }
                }

                // Sheet 2: Branch Network (if present)
                if ($sheetCount > self::SHEET_BRANCH_NETWORK) {
                    Branch::where('import_log_id', $importLog->id)->delete();
                    $spreadsheet->setActiveSheetIndex(self::SHEET_BRANCH_NETWORK);
                    $branches = $this->buildBranchesFromUpload($spreadsheet, $importLog);

                    if (! empty($branches)) {
                        Branch::insert($branches);
                        $totalImported += count($branches);
                    }
                }

                if ($totalImported === 0) {
                    throw new \RuntimeException('No valid data rows were found in any sheet of the uploaded file.');
                }

                $importLog->update(['status' => 'completed']);
            });
        } catch (\Throwable $exception) {
            $importLog->update(['status' => 'failed']);

            return redirect()->route($redirectRoute)
                ->with('selected_import_log_id', $importLog->id)
                ->withErrors([
                    'file' => 'Import failed: '.$exception->getMessage(),
                ]);
        }

        return redirect()->route($redirectRoute)
            ->with('selected_import_log_id', $importLog->id)
            ->with('toast', [
                'message' => 'Data imported into the database successfully.',
                'type' => 'success',
            ]);
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
