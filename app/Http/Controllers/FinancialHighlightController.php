<?php

namespace App\Http\Controllers;

use App\Models\FinancialHighlightImport;
use App\Support\NepaliFiscalCalendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialHighlightController extends Controller
{
    private const FIELDS = [
        'solvencyratiox' => 'solvency_ratio',
        'returnonequity' => 'return_on_equity',
        'earningspersharenpr' => 'earnings_per_share',
        'networthnpr' => 'net_worth',
        'netprofitmargin' => 'net_profit_margin',
        'liquidityratio' => 'liquidity_ratio',
        'investmentyield' => 'investment_yield',
    ];

    public function create(): View
    {
        return view('financial-highlights.upload', [
            'fiscalYears' => NepaliFiscalCalendar::fiscalYearOptions(),
            'quarters' => $this->quarters(),
            'recentImports' => FinancialHighlightImport::with('user')->latest()->limit(8)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fiscal_year' => ['required', 'string', 'max:20'],
            'quarter' => ['required', 'integer', 'between:1,4'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ]);
        $file = $request->file('file');
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '-', $file->getClientOriginalName());
        $path = $file->storeAs(
            'uploads/financial-highlights/'.str_replace('/', '-', $validated['fiscal_year']).'/q'.$validated['quarter'],
            now()->format('YmdHis').'-'.$safeName,
            'public'
        );
        $import = FinancialHighlightImport::create([
            'user_id' => auth()->id(), 'file_name' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'fiscal_year' => $validated['fiscal_year'], 'quarter' => $validated['quarter'],
        ]);

        return redirect()->route('financial-highlights.import', ['import_id' => $import->id])
            ->with('toast', ['message' => 'Financial highlights file uploaded. It is ready to import.', 'type' => 'success']);
    }

    public function importPage(Request $request): View
    {
        $imports = FinancialHighlightImport::whereIn('status', ['pending', 'failed'])->latest()->get();
        $selected = $imports->firstWhere('id', $request->integer('import_id')) ?? $imports->first();
        $historyQuery = FinancialHighlightImport::with(['user', 'highlights'])->where('status', 'completed');
        if ($request->filled('fiscal_year')) $historyQuery->where('fiscal_year', $request->string('fiscal_year'));
        if ($request->filled('quarter')) $historyQuery->where('quarter', $request->integer('quarter'));

        return view('financial-highlights.import', [
            'imports' => $imports,
            'selected' => $selected,
            'quarters' => $this->quarters(),
            'history' => $historyQuery->latest('imported_at')->get(),
            'fiscalYears' => FinancialHighlightImport::where('status', 'completed')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year'),
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate(['import_id' => ['required', 'exists:financial_highlight_imports,id']]);
        $import = FinancialHighlightImport::findOrFail($validated['import_id']);
        if ($import->status === 'completed') {
            return back()->with('toast', ['message' => 'This file has already been imported.', 'type' => 'warning']);
        }

        try {
            DB::transaction(function () use ($import) {
                $import->update(['status' => 'processing', 'error_message' => null]);
                $rows = IOFactory::load(Storage::disk('public')->path($import->file_name))->getActiveSheet()->toArray(null, true, true, false);
                $headerIndex = $this->headerIndex($rows);
                if ($headerIndex === null) {
                    throw new \RuntimeException('Required financial highlight headings were not found. Download the template and use its headings.');
                }
                $headers = array_map(fn ($value) => $this->normalize((string) $value), $rows[$headerIndex]);
                $records = [];
                foreach (array_slice($rows, $headerIndex + 1) as $row) {
                    $record = ['fiscal_year' => $import->fiscal_year, 'quarter' => $import->quarter];
                    $hasValue = false;
                    foreach ($headers as $column => $header) {
                        if (! isset(self::FIELDS[$header])) continue;
                        $value = $row[$column] ?? null;
                        if ($value !== null && trim((string) $value) !== '') {
                            if (! is_numeric(str_replace([',', '%'], '', (string) $value))) {
                                throw new \RuntimeException('A non-numeric value was found in '.array_search(self::FIELDS[$header], self::FIELDS, true).'.');
                            }
                            $record[self::FIELDS[$header]] = (float) str_replace([',', '%'], '', (string) $value);
                            $hasValue = true;
                        } else {
                            $record[self::FIELDS[$header]] = null;
                        }
                    }
                    if ($hasValue) $records[] = $record;
                }
                if ($records === []) throw new \RuntimeException('No financial highlight data rows were found.');

                $import->highlights()->delete();
                $import->highlights()->createMany($records);
                $import->update(['status' => 'completed', 'imported_rows' => count($records), 'imported_at' => now()]);
            });
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return back()->withErrors(['file' => 'Import failed: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('financial-highlights.import')->with('toast', ['message' => 'Financial highlights imported successfully.', 'type' => 'success']);
    }

    public function history(Request $request): RedirectResponse
    {
        return redirect()->route('financial-highlights.import', $request->only(['fiscal_year', 'quarter']));
    }

    public function destroy(FinancialHighlightImport $financialHighlightImport): RedirectResponse
    {
        Storage::disk('public')->delete($financialHighlightImport->file_name);
        $financialHighlightImport->delete();
        return back()->with('toast', ['message' => 'Financial highlight import and its data were deleted.', 'type' => 'success']);
    }

    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['Solvency Ratio (x)', 'Return on Equity (%)', 'Earnings per Share (NPR)', 'Net Worth (NPR)', 'Net Profit Margin (%)', 'Liquidity Ratio', 'Investment Yield (%)']]);
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        foreach (range('A', 'G') as $column) $sheet->getColumnDimension($column)->setAutoSize(true);
        return response()->streamDownload(fn () => (new Xlsx($spreadsheet))->save('php://output'), 'financial-highlights-template.xlsx');
    }

    private function headerIndex(array $rows): ?int
    {
        foreach ($rows as $index => $row) {
            $normalized = array_map(fn ($value) => $this->normalize((string) $value), $row);
            if (count(array_intersect(array_keys(self::FIELDS), $normalized)) === count(self::FIELDS)) return $index;
        }
        return null;
    }

    private function normalize(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', trim($value)));
    }

    private function quarters(): array
    {
        return [1 => '1st Quarter', 2 => '2nd Quarter', 3 => '3rd Quarter', 4 => '4th Quarter'];
    }
}
