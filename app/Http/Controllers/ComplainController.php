<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\GrievanceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplainController extends Controller
{
    public function index(): View
    {
        $grievances = Complain::with('grievanceType')->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        return view('complains.index', compact('grievances'));
    }

    public function create(): View
    {
        return view('complains.create', ['grievanceTypes' => GrievanceType::orderBy('name')->get()]);
    }

    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Grievance Data');
        $sheet->fromArray([[
            'Grievance Type ID',
            'Received Num',
            'Resolved Num',
            'Average Resolution Time (Days)',
            'Status',
        ]]);
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle('Instructions');
        $instructions->fromArray([
            ['Grievance Import Instructions'],
            ['1. Keep the headings in the Grievance Data sheet unchanged.'],
            ['2. Enter the grievance type ID from Grievance Types master data for each row.'],
            ['3. Enter the overall Average Resolution Time once in the first data row. It applies to the complete report.'],
            ['4. Pending and overall Resolution Rate are calculated automatically during import.'],
            ['5. Resolved Num cannot exceed Received Num.'],
        ]);
        $instructions->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructions->getColumnDimension('A')->setWidth(105);

        $typeSheet = $spreadsheet->createSheet();
        $typeSheet->setTitle('Grievance Types');
        $typeSheet->fromArray([['ID', 'Grievance Type']]);
        $typeSheet->fromArray(
            GrievanceType::orderBy('id')->get()->map(fn (GrievanceType $type) => [$type->id, $type->name])->all(),
            null,
            'A2'
        );
        $typeSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $typeSheet->getColumnDimension('A')->setWidth(12);
        $typeSheet->getColumnDimension('B')->setWidth(40);
        $typeSheet->freezePane('A2');

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            'grievance-import-template.xlsx'
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'grievance_type' => ['required', 'integer', 'exists:grievance_types,id'],
            'received_num' => ['required', 'integer', 'min:0'],
            'resolved_num' => ['required', 'integer', 'min:0', 'lte:received_num'],
            'average_resolution_time' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Complain::create($this->withCalculatedFields($validated));

        return redirect()->route('grievances.index')->with('toast', [
            'message' => 'Grievance record created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(Complain $complain): View
    {
        return view('complains.edit', [
            'complain' => $complain,
            'grievanceTypes' => GrievanceType::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Complain $complain): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'grievance_type' => ['required', 'integer', 'exists:grievance_types,id'],
            'received_num' => ['required', 'integer', 'min:0'],
            'resolved_num' => ['required', 'integer', 'min:0', 'lte:received_num'],
            'average_resolution_time' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $complain->update($this->withCalculatedFields($validated));

        return redirect()->route('grievances.index')->with('toast', [
            'message' => 'Grievance record updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Complain $complain): RedirectResponse
    {
        $complain->delete();

        return redirect()->route('grievances.index')->with('toast', [
            'message' => 'Grievance record deleted successfully.',
            'type' => 'success',
        ]);
    }

    private function withCalculatedFields(array $validated): array
    {
        $received = (int) $validated['received_num'];
        $resolved = (int) $validated['resolved_num'];
        $validated['pending_num'] = $received - $resolved;
        $validated['resolution_rate'] = $received > 0 ? round(($resolved / $received) * 100, 2) : 0;

        return $validated;
    }
}
