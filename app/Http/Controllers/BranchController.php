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
            ->orderBy('year', 'desc')
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
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'number' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('toast', [
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
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_id' => ['required', 'exists:districts,district_id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'number' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('toast', [
            'message' => 'Branch updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('toast', [
            'message' => 'Branch deleted successfully.',
            'type' => 'success',
        ]);
    }
}