<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\District;
use App\Models\Province;
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
        return view('branches.index', compact('branches'));
    }

    public function create(): View
    {
        $provinces = Province::orderBy('province_name')->get();
        $districts = District::orderBy('district_name')->get();
        return view('branches.create', compact('provinces', 'districts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_code' => ['required', 'integer', 'min:1', 'unique:branch_network,branch_code'],
            'ext_branch_code' => ['required', 'string', 'max:10', 'unique:branch_network,ext_branch_code'],
            'branch_name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'local_level' => ['nullable', 'integer', 'min:1'],
            'address' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['display_name'] = ($validated['display_name'] ?? '') ?: $validated['branch_name'];

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
        return view('branches.edit', compact('branch', 'provinces', 'districts'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'branch_code' => ['required', 'integer', 'min:1', Rule::unique('branch_network', 'branch_code')->ignore($branch->id)],
            'ext_branch_code' => ['required', 'string', 'max:10', Rule::unique('branch_network', 'ext_branch_code')->ignore($branch->id)],
            'branch_name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'local_level' => ['nullable', 'integer', 'min:1'],
            'address' => ['nullable', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['display_name'] = ($validated['display_name'] ?? '') ?: $validated['branch_name'];

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
}