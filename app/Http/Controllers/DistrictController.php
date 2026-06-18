<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function index(): View
    {
        $districts = District::with('province')
            ->withCount('branches')
            ->orderBy('district_name')
            ->get();
        return view('districts.index', compact('districts'));
    }

    public function create(): View
    {
        $provinces = Province::orderBy('province_name')->get();
        return view('districts.create', compact('provinces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
        ]);

        District::create($validated);

        return redirect()->route('districts.index')->with('toast', [
            'message' => 'District created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(District $district): View
    {
        $provinces = Province::orderBy('province_name')->get();
        return view('districts.edit', compact('district', 'provinces'));
    }

    public function update(Request $request, District $district): RedirectResponse
    {
        $validated = $request->validate([
            'province_id' => ['required', 'exists:provinces,province_id'],
            'district_name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
        ]);

        $district->update($validated);

        return redirect()->route('districts.index')->with('toast', [
            'message' => 'District updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(District $district): RedirectResponse
    {
        $district->delete();

        return redirect()->route('districts.index')->with('toast', [
            'message' => 'District deleted successfully.',
            'type' => 'success',
        ]);
    }
}