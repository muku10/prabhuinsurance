<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\District;
use App\Models\Province;
use App\Support\NepaliFiscalCalendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with(['province', 'district'])
            ->orderBy('branch_code')
            ->get();
        $monthNames = NepaliFiscalCalendar::monthNames();

        return view('branches.index', compact('branches', 'monthNames'));
    }

    public function create(): View
    {
        $provinces = Province::orderBy('province_name')->get();
        $districts = District::orderBy('district_name')->get();
        [$fiscalYears, $monthNames] = $this->periodOptions();

        return view('branches.create', compact('provinces', 'districts', 'fiscalYears', 'monthNames'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedBranchData($request);

        Branch::create($validated);

        return redirect()->route($request->input('_redirect') === 'master-data' ? 'master-data.index' : 'branches.index')->with('toast', [
            'message' => 'Branch created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(Branch $branch): View
    {
        $provinces = Province::orderBy('province_name')->get();
        $districts = District::orderBy('district_name')->get();
        [$fiscalYears, $monthNames] = $this->periodOptions();

        return view('branches.edit', compact('branch', 'provinces', 'districts', 'fiscalYears', 'monthNames'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $this->validatedBranchData($request, $branch);

        $branch->update($validated);

        return redirect()->route($request->input('_redirect') === 'master-data' ? 'master-data.index' : 'branches.index')->with('toast', [
            'message' => 'Branch updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Request $request, Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route($request->input('_redirect') === 'master-data' ? 'master-data.index' : 'branches.index')->with('toast', [
            'message' => 'Branch deleted successfully.',
            'type' => 'success',
        ]);
    }

    private function validatedBranchData(Request $request, ?Branch $branch = null): array
    {
        $branchCodeRule = Rule::unique('branch_network', 'branch_code');
        $extBranchCodeRule = Rule::unique('branch_network', 'ext_branch_code');

        if ($branch) {
            $branchCodeRule->ignore($branch->id);
            $extBranchCodeRule->ignore($branch->id);
        }

        $validated = $request->validate([
            'branch_code' => [
                'required',
                'integer',
                'min:1',
                $branchCodeRule,
            ],
            'ext_branch_code' => [
                'required',
                'string',
                'max:10',
                $extBranchCodeRule,
            ],
            'branch_name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'local_level' => ['nullable', 'integer', 'min:1'],
            'address' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'inactive_fiscal_year' => ['nullable', 'required_if:status,inactive', 'string', 'max:255'],
            'inactive_month' => ['nullable', 'required_if:status,inactive', 'integer', 'min:1', 'max:12'],
        ]);

        $validated['display_name'] = ($validated['display_name'] ?? '') ?: $validated['branch_name'];
        $this->clearInactivePeriodWhenActive($validated);

        return $validated;
    }

    private function periodOptions(): array
    {
        return [
            NepaliFiscalCalendar::fiscalYearOptions(),
            NepaliFiscalCalendar::monthNames(),
        ];
    }

    private function clearInactivePeriodWhenActive(array &$validated): void
    {
        if (($validated['status'] ?? 'active') === 'active') {
            $validated['inactive_fiscal_year'] = null;
            $validated['inactive_month'] = null;
        }
    }
}
