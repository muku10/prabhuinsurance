<?php

namespace App\Http\Controllers;

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
    public function index(): View
    {
        $importLogs = ImportLog::with('user')->latest('date')->get();
        $monthNames = $this->bsMonthNames();

        return view('import-logs.index', compact('importLogs', 'monthNames'));
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
        $availableImportLogs = ImportLog::whereDoesntHave('transactions')
            ->latest('date')
            ->latest('id')
            ->get();
        $selectedImportLog = $availableImportLogs->firstWhere('id', session('selected_import_log_id')) ?? $availableImportLogs->first();
        $importHistory = ImportLog::with(['user', 'transactions'])
            ->withCount('transactions')
            ->whereHas('transactions')
            ->latest('date')
            ->latest('id')
            ->get();

        return view('import-logs.import', compact('availableImportLogs', 'monthNames', 'selectedImportLog', 'importHistory'));
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
        $deletedRows = 0;

        DB::transaction(function () use ($importLog, &$deletedRows) {
            $deletedRows = Transaction::where('import_log_id', $importLog->id)->delete();
            $importLog->update(['status' => 'pending']);
        });

        return redirect()->route('upload.import-module')->with('toast', [
            'message' => $deletedRows > 0
                ? 'Imported database rows deleted successfully. The uploaded file is still available for re-import.'
                : 'No imported database rows were found for this file.',
            'type' => $deletedRows > 0 ? 'success' : 'warning',
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

                Transaction::where('import_log_id', $importLog->id)->delete();

                $importBatchToken = (string) Str::uuid();
                $transactions = $this->buildTransactionsFromUpload($importLog, $importLog->fiscal_year, (int) $importLog->month, $importBatchToken);

                if (empty($transactions)) {
                    throw new \RuntimeException('No valid transaction rows were found in the uploaded file.');
                }

                Transaction::insert($transactions);

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
                'message' => 'Transactions imported into the database successfully.',
                'type' => 'success',
            ]);
    }

    private function buildTransactionsFromUpload(ImportLog $importLog, string $fiscalYear, int $selectedMonth, string $importBatchToken): array
    {
        $filePath = Storage::disk('public')->path($importLog->file_name);
        $worksheet = IOFactory::load($filePath)->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows);

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

    private function findHeaderRowIndex(array $rows): ?int
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
