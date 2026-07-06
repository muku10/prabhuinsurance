<x-app-layout>
    <x-slot name="title">Database Import History</x-slot>
    <x-slot name="crumbs">Database Import History</x-slot>

    <div class="card mb-4">
        <div class="card-head">
            <h2>Filter Import History</h2>
            <a href="{{ route('upload.import-module') }}" class="btn btn-outline">Back to Import</a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('upload.database-history') }}" class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-6 items-end">
                <div class="field">
                    <label for="fiscal_year">Fiscal Year</label>
                    <select class="input" name="fiscal_year" id="fiscal_year">
                        <option value="">All Fiscal Years</option>
                        @foreach ($fiscalYears as $fiscalYear)
                            <option value="{{ $fiscalYear }}" @selected($selectedFiscalYear === $fiscalYear)>{{ $fiscalYear }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="month">Month</label>
                    <select class="input" name="month" id="month">
                        <option value="">All Months</option>
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((int) $selectedMonth === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('upload.database-history') }}" class="btn btn-ghost" style="border:1px solid var(--line);">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>Database Import History</h2>
            <div class="text-muted" style="font-size:12px;">{{ $importHistory->count() }} imported file(s)</div>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table class="t">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Type</th>
                            <th>File Name</th>
                            <th>Fiscal Year</th>
                            <th>Month</th>
                            <th>Records</th>
                            <th>Imported By</th>
                            <th>Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($importHistory as $history)
                            @php
                                $recordCount = match ($history->upload_type ?? 'premium') {
                                    'intimation_claim' => $history->intimation_claims_count,
                                    'paid_claim' => $history->paid_claims_count,
                                    'withdrawal_claim' => $history->withdrawal_claims_count,
                                    'outstanding_claim' => $history->outstanding_claims_count,
                                    default => $history->premiums_count,
                                };
                                $typeLabel = ucfirst(str_replace('_', ' ', $history->upload_type ?? 'premium'));
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $typeLabel }}</td>
                                <td>{{ basename($history->file_name) }}</td>
                                <td>{{ $history->fiscal_year }}</td>
                                <td>{{ $monthNames[$history->month] ?? $history->month }}</td>
                                <td>{{ $recordCount }}</td>
                                <td>{{ $history->user?->full_name ?? $history->user?->email ?? 'System' }}</td>
                                <td><span class="badge success">Imported</span></td>
                                <td style="text-align:right; white-space:nowrap;">
                                    <button
                                        type="button"
                                        class="btn btn-ghost"
                                        style="border:1px solid var(--line); color:#B91C1C;"
                                        x-on:click="$store.confirm.askForForm({
                                            title: 'Delete imported data',
                                            message: `Delete imported database rows for ${@js(basename($history->file_name))}? The uploaded file will remain available.`,
                                            confirmText: 'Delete imported rows',
                                            cancelText: 'Cancel',
                                            formId: 'delete-imported-data-{{ $history->id }}'
                                        })"
                                    >Delete</button>
                                    <form id="delete-imported-data-{{ $history->id }}" method="POST" action="{{ route('upload.import-module.destroy', $history->id) }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-muted" style="text-align:center; padding:24px;">No database import history found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
