<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolicyController extends Controller
{
    public function index(): View
    {
        $policies = Policy::with('parent')->orderBy('policy_name')->get();
        return view('policies.index', compact('policies'));
    }

    public function create(): View
    {
        $parents = Policy::whereNull('parent_id')->orderBy('policy_name')->get();
        return view('policies.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:policies,policy_id'],
            'policy_name' => ['required', 'string', 'max:255'],
            'policy_name_np' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Policy::create($validated);

        return redirect()->route('policies.index')->with('toast', [
            'message' => 'Policy created successfully.',
            'type' => 'success',
        ]);
    }

    public function edit(Policy $policy): View
    {
        $parents = Policy::whereNull('parent_id')
            ->where('policy_id', '!=', $policy->policy_id)
            ->orderBy('policy_name')
            ->get();
        return view('policies.edit', compact('policy', 'parents'));
    }

    public function update(Request $request, Policy $policy): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:policies,policy_id'],
            'policy_name' => ['required', 'string', 'max:255'],
            'policy_name_np' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] && $validated['parent_id'] == $policy->policy_id) {
            return back()->withErrors(['parent_id' => 'A policy cannot be its own parent.']);
        }

        $policy->update($validated);

        return redirect()->route('policies.index')->with('toast', [
            'message' => 'Policy updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Policy $policy): RedirectResponse
    {
        $policy->delete();

        return redirect()->route('policies.index')->with('toast', [
            'message' => 'Policy deleted successfully.',
            'type' => 'success',
        ]);
    }
}