<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplainController extends Controller
{
    public function index(): View
    {
        $complains = Complain::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        return view('complains.index', compact('complains'));
    }

    public function create(): View
    {
        return view('complains.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'complain_type' => ['required', 'string', 'max:255'],
            'received_num' => ['required', 'integer', 'min:0'],
            'resolved_num' => ['required', 'integer', 'min:0'],
            'pending_num' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Complain::create($validated);

        return redirect()->route('complains.index')->with('toast', [
            'message' => 'Complain record created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(Complain $complain): View
    {
        return view('complains.edit', compact('complain'));
    }

    public function update(Request $request, Complain $complain): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'complain_type' => ['required', 'string', 'max:255'],
            'received_num' => ['required', 'integer', 'min:0'],
            'resolved_num' => ['required', 'integer', 'min:0'],
            'pending_num' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $complain->update($validated);

        return redirect()->route('complains.index')->with('toast', [
            'message' => 'Complain record updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Complain $complain): RedirectResponse
    {
        $complain->delete();

        return redirect()->route('complains.index')->with('toast', [
            'message' => 'Complain record deleted successfully.',
            'type' => 'success',
        ]);
    }
}