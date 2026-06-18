<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function index(): View
    {
        $provinces = Province::withCount(['districts', 'branches'])
            ->orderBy('province_name')
            ->get();
        return view('provinces.index', compact('provinces'));
    }

    public function create(): View
    {
        return view('provinces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'province_name' => ['required', 'string', 'max:255', 'unique:provinces,province_name'],
            'code' => ['nullable', 'string', 'max:10'],
        ]);

        Province::create($validated);

        return redirect()->route('provinces.index')->with('toast', [
            'message' => 'Province created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(Province $province): View
    {
        return view('provinces.edit', compact('province'));
    }

    public function update(Request $request, Province $province): RedirectResponse
    {
        $validated = $request->validate([
            'province_name' => ['required', 'string', 'max:255', 'unique:provinces,province_name,' . $province->province_id . ',province_id'],
            'code' => ['nullable', 'string', 'max:10'],
        ]);

        $province->update($validated);

        return redirect()->route('provinces.index')->with('toast', [
            'message' => 'Province updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Province $province): RedirectResponse
    {
        $province->delete();

        return redirect()->route('provinces.index')->with('toast', [
            'message' => 'Province deleted successfully.',
            'type' => 'success',
        ]);
    }
}