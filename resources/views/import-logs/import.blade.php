<x-app-layout>
    <x-slot name="title">Import to Database</x-slot>
    <x-slot name="crumbs">Import to Database</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Select uploaded file</h2>
                    <a href="{{ route('upload.create') }}" class="btn btn-outline">Back to Upload</a>
                </div>
                <div class="card-body">
                    @if ($errors->has('import_log_id') || $errors->has('file'))
                        <div style="border:1px solid #FCA5A5; background:#FEF2F2; color:#991B1B; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px;">
                            {{ $errors->first('import_log_id') ?: $errors->first('file') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('upload.import-module.store') }}" id="importModuleForm">
                        @csrf
                        <div class="field">
                            <label for="import_log_id">Uploaded File</label>
                            <select class="input" name="import_log_id" id="import_log_id" required>
                                <option value="">Select uploaded file</option>
                                @foreach ($availableImportLogs as $log)
                                    <option
                                        value="{{ $log->id }}"
                                        data-fiscal-year="{{ $log->fiscal_year }}"
                                        data-month="{{ $monthNames[$log->month] ?? $log->month }}"
                                        data-status="{{ ucfirst($log->status) }}"
                                        @selected((int) old('import_log_id', $selectedImportLog?->id) === $log->id)
                                    >
                                        {{ basename($log->file_name) }} - FY {{ $log->fiscal_year }} - {{ $monthNames[$log->month] ?? $log->month }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($availableImportLogs->isEmpty())
                                <div class="text-muted" style="font-size:13px; margin-top:10px;">All uploaded files are already imported. Upload a new file or delete imported rows from the history table below.</div>
                            @endif
                        </div>

                        <div class="grid cols-3 mt-4">
                            <div class="kpi">
                                <div class="label">Fiscal Year</div>
                                <div class="value" id="selectedFiscalYear">{{ $selectedImportLog?->fiscal_year ?? '-' }}</div>
                            </div>
                            <div class="kpi">
                                <div class="label">Month</div>
                                <div class="value" id="selectedMonth">{{ $selectedImportLog ? ($monthNames[$selectedImportLog->month] ?? $selectedImportLog->month) : '-' }}</div>
                            </div>
                            <div class="kpi">
                                <div class="label">Status</div>
                                <div class="value" id="selectedStatus">{{ $selectedImportLog ? ucfirst($selectedImportLog->status) : '-' }}</div>
                            </div>
                        </div>

                        <div class="text-muted" style="font-size:13px; margin-top:14px;">Choose an uploaded file and the fiscal year and month will appear automatically from that upload log. Files already imported into the transactions table are hidden from this list.</div>

                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitImportBtn" @disabled(! $selectedImportLog)>
                                Import to Database
                            </button>
                            <a href="{{ route('upload.create') }}" class="btn btn-outline">Upload Another File</a>
                            <a href="{{ route('import-logs.index') }}" class="btn btn-ghost" style="border:1px solid var(--line);">View Upload History</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-head"><h2>Import Summary</h2></div>
                <div class="card-body" style="font-size:13.5px; color: var(--ink-soft); line-height:1.7;">
                    <ul style="padding-left:18px; margin:0;">
                        <li>Select any uploaded file that is not yet imported.</li>
                        <li>Fiscal year and month are read from the selected upload.</li>
                        <li>Imported files move to the database history table below.</li>
                        <li>If import fails, the log status changes to failed.</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-head"><h2>Upload History</h2></div>
                <div class="card-body" style="padding:0;">
                    @forelse ($availableImportLogs->take(5) as $log)
                        <div class="val-row">
                            <span class="badge {{ $log->status === 'completed' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($log->status) }}</span>
                            <div>
                                <strong>{{ basename($log->file_name) }}</strong>
                                <div class="text-muted" style="font-size:12px;">FY {{ $log->fiscal_year }} - {{ $monthNames[$log->month] ?? $log->month }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="val-row text-muted">No pending uploaded files available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-head">
            <h2>Database Import History</h2>
            <div class="text-muted" style="font-size:12px;">Imported rows only</div>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table class="t">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Fiscal Year</th>
                            <th>Month</th>
                            <th>Rows</th>
                            <th>Import Token</th>
                            <th>Imported By</th>
                            <th>Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($importHistory as $history)
                            @php
                                $historyToken = $history->transactions->first()?->import_batch_token ?? '-';
                            @endphp
                            <tr>
                                <td>{{ basename($history->file_name) }}</td>
                                <td>{{ $history->fiscal_year }}</td>
                                <td>{{ $monthNames[$history->month] ?? $history->month }}</td>
                                <td>{{ $history->transactions_count }}</td>
                                <td>{{ $historyToken }}</td>
                                <td>{{ $history->user?->full_name ?? $history->user?->email ?? 'System' }}</td>
                                <td>
                                    <span class="badge success">Imported</span>
                                </td>
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
                                <td colspan="8" class="text-muted" style="text-align:center; padding:24px;">No database import history available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('import_log_id');
            const fiscalYear = document.getElementById('selectedFiscalYear');
            const month = document.getElementById('selectedMonth');
            const status = document.getElementById('selectedStatus');
            const submitButton = document.getElementById('submitImportBtn');

            const updateSelection = () => {
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    fiscalYear.textContent = '-';
                    month.textContent = '-';
                    status.textContent = '-';
                    submitButton.disabled = true;
                    submitButton.textContent = 'Import to Database';
                    return;
                }

                fiscalYear.textContent = selectedOption.dataset.fiscalYear || '-';
                month.textContent = selectedOption.dataset.month || '-';
                status.textContent = selectedOption.dataset.status || '-';

                submitButton.disabled = false;
                submitButton.textContent = 'Import to Database';
            };

            select.addEventListener('change', updateSelection);
            updateSelection();
        });
    </script>
</x-app-layout>
