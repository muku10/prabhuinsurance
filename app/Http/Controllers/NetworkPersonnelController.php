<?php

namespace App\Http\Controllers;

use App\Models\NetworkPersonnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class NetworkPersonnelController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        NetworkPersonnel::create($this->validatedData($request));
        $this->clearDashboardCache();

        return $this->redirectWithToast('Agent/surveyor total created successfully.');
    }

    public function update(Request $request, NetworkPersonnel $networkPersonnel): RedirectResponse
    {
        $networkPersonnel->update($this->validatedData($request, $networkPersonnel));
        $this->clearDashboardCache();

        return $this->redirectWithToast('Agent/surveyor total updated successfully.');
    }

    public function destroy(NetworkPersonnel $networkPersonnel): RedirectResponse
    {
        $networkPersonnel->delete();
        $this->clearDashboardCache();

        return $this->redirectWithToast('Agent/surveyor total deleted successfully.');
    }

    private function validatedData(Request $request, ?NetworkPersonnel $networkPersonnel = null): array
    {
        $periodRule = Rule::unique('network_personnel', 'month')
            ->where(fn ($query) => $query
                ->where('type', $request->input('type'))
                ->where('fiscal_year', $request->input('fiscal_year')));

        if ($networkPersonnel) {
            $periodRule->ignore($networkPersonnel->id);
        }

        return $request->validate([
            'type' => ['required', 'in:agent,surveyor'],
            'fiscal_year' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'month' => ['required', 'integer', 'between:1,12', $periodRule],
            'number' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function clearDashboardCache(): void
    {
        Cache::forget('public-dashboard:data:v10');
    }

    private function redirectWithToast(string $message): RedirectResponse
    {
        return redirect()->route('master-data.index')->with('toast', [
            'message' => $message,
            'type' => 'success',
        ]);
    }
}
