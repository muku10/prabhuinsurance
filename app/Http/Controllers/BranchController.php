<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with(['province', 'district'])
            ->orderBy('fiscal_year', 'desc')
            ->orderBy('month', 'desc')
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
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'number_of_branch' => ['required', 'integer', 'min:0'],
            'number_of_agents' => ['required', 'integer', 'min:0'],
            'number_of_surveyors' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('toast', [
            'message' => 'Branch Network record created successfully.',
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
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'fiscal_year' => ['required', 'string', 'max:255'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'number_of_branch' => ['required', 'integer', 'min:0'],
            'number_of_agents' => ['required', 'integer', 'min:0'],
            'number_of_surveyors' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('toast', [
            'message' => 'Branch Network record updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('toast', [
            'message' => 'Branch Network record deleted successfully.',
            'type' => 'success',
        ]);
    }
}