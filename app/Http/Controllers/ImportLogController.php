<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        $recentUploads = ImportLog::latest('date')->limit(3)->get();
        $today = now();
        $currentPeriod = $this->currentBsPeriod($today);
        $monthNames = $this->bsMonthNames();
        $submissionDateBs = $this->currentBsDate($today, $monthNames);
        $fiscalYearOptions = $this->fiscalYearOptions($currentPeriod['fiscal_year']);

        return view('import-logs.create', compact(
            'recentUploads',
            'currentPeriod',
            'monthNames',
            'submissionDateBs',
            'fiscalYearOptions'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $today = now();
        $currentPeriod = $this->currentBsPeriod($today);

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

        ImportLog::create([
            'date' => $today->toDateString(),
            'user_id' => auth()->id(),
            'file_name' => $storedFile,
            'fiscal_year' => $validated['fiscal_year'],
            'month' => $validated['month'],
            'status' => 'completed',
        ]);

        return redirect()->route('upload.create')->with('toast', [
            'message' => 'File uploaded and log saved successfully.',
            'type' => 'success',
        ]);
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
