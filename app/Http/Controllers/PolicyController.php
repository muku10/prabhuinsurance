<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'policy_id' => ['required', 'integer', 'min:1', 'unique:policies,policy_id'],
            'parent_id' => ['nullable', 'exists:policies,policy_id'],
            'policy_name' => ['required', 'string', 'max:255'],
            'policy_name_np' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Policy::create($validated);

        return redirect()->route('master-data.index')->with('toast', [
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
            'policy_id' => ['required', 'integer', 'min:1', Rule::unique('policies', 'policy_id')->ignore($policy->policy_id, 'policy_id')],
            'parent_id' => ['nullable', 'exists:policies,policy_id'],
            'policy_name' => ['required', 'string', 'max:255'],
            'policy_name_np' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] && $validated['parent_id'] == $validated['policy_id']) {
            return back()->withErrors(['parent_id' => 'A policy cannot be its own parent.'])->withInput();
        }

        // When the primary key changes, update it explicitly then save the rest.
        if ((int) $validated['policy_id'] !== (int) $policy->policy_id) {
            $policy->policy_id = $validated['policy_id'];
        }

        $policy->parent_id = $validated['parent_id'];
        $policy->policy_name = $validated['policy_name'];
        $policy->policy_name_np = $validated['policy_name_np'];
        $policy->code = $validated['code'] ?? null;
        $policy->status = $validated['status'];
        $policy->save();

        return redirect()->route('master-data.index')->with('toast', [
            'message' => 'Policy updated successfully.',
            'type' => 'success',
        ]);
    }

    public function destroy(Policy $policy): RedirectResponse
    {
        $policy->delete();

        return redirect()->route('master-data.index')->with('toast', [
            'message' => 'Policy deleted successfully.',
            'type' => 'success',
        ]);
    }
}