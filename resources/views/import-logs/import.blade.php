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
                    @if ($errors->has('upload_type') || $errors->has('import_log_id') || $errors->has('file'))
                        <div style="border:1px solid #FCA5A5; background:#FEF2F2; color:#991B1B; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px;">
                            {{ $errors->first('upload_type') ?: ($errors->first('import_log_id') ?: $errors->first('file')) }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('upload.import-module.store') }}" id="importModuleForm">
                        @csrf
                        <div class="field">
                            <label for="upload_type">File Type</label>
                            <select class="input" name="upload_type" id="upload_type" required>
                                <option value="">Select import type</option>
                                @foreach ($availableUploadTypes as $uploadType)
                                    <option
                                        value="{{ $uploadType }}"
                                        @selected(old('upload_type', $selectedImportLog ? strtolower(trim($selectedImportLog->upload_type ?? 'premium')) : '') === $uploadType)
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $uploadType)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field mt-4" id="uploadedFileField" hidden>
                            <label for="import_log_id">Uploaded File</label>
                            <select class="input" name="import_log_id" id="import_log_id" required>
                                <option value="">Select uploaded file</option>
                                @foreach ($availableImportLogs as $log)
                                    <option
                                        value="{{ $log->id }}"
                                        data-upload-type="{{ strtolower(trim($log->upload_type ?? 'premium')) }}"
                                        data-fiscal-year="{{ $log->fiscal_year }}"
                                        data-month="{{ $monthNames[$log->month] ?? $log->month }}"
                                        data-type="{{ ucfirst(str_replace('_', ' ', $log->upload_type ?? 'premium')) }}"
                                        data-status="{{ ucfirst($log->status) }}"
                                        @selected((int) old('import_log_id', $selectedImportLog?->id) === $log->id)
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $log->upload_type ?? 'premium')) }} - {{ basename($log->file_name) }} - FY {{ $log->fiscal_year }} - {{ $monthNames[$log->month] ?? $log->month }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($availableImportLogs->isEmpty())
                                <div class="text-muted" style="font-size:13px; margin-top:10px;">All uploaded files are already imported. Upload a new file or delete imported rows from the history table below.</div>
                            @endif
                            <div class="text-muted" id="noFilesForType" style="font-size:13px; margin-top:10px;" hidden>No pending uploaded files are available for this type.</div>
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
                                <div class="label">Type</div>
                                <div class="value" id="selectedType">{{ $selectedImportLog ? ucfirst(str_replace('_', ' ', $selectedImportLog->upload_type ?? 'premium')) : '-' }}</div>
                            </div>
                            <div class="kpi">
                                <div class="label">Status</div>
                                <div class="value" id="selectedStatus">{{ $selectedImportLog ? ucfirst($selectedImportLog->status) : '-' }}</div>
                            </div>
                        </div>

                        <div class="text-muted" style="font-size:13px; margin-top:14px;">Choose an import type first. The file list will then show only uploaded files associated with that type. Files already imported into the database are hidden.</div>

                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitImportBtn" @disabled(! $selectedImportLog)>
                                Import to Database
                            </button>
                            <a href="{{ route('upload.create') }}" class="btn btn-outline">Upload Another File</a>
                            <a href="{{ route('import-logs.index') }}" class="btn btn-ghost" style="border:1px solid var(--line);">View Upload History</a>
                            <a href="{{ route('upload.database-history') }}" class="btn btn-ghost" style="border:1px solid var(--line);">View Import History</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-head"><h2>Import Summary</h2></div>
                <div class="card-body" style="font-size:13.5px; color: var(--ink-soft); line-height:1.7;">
                    <ul style="list-style:disc outside; padding-left:22px; margin:0; display:grid; gap:6px; color:var(--brand);">
                        <li><span style="color:var(--ink-soft);">Select the import type before choosing a file.</span></li>
                        <li><span style="color:var(--ink-soft);">Only files associated with that type will be shown.</span></li>
                        <li><span style="color:var(--ink-soft);">Fiscal year, month, and type are read from the selected upload.</span></li>
                        <li><span style="color:var(--ink-soft);">Imported files move to the database history table below.</span></li>
                        <li><span style="color:var(--ink-soft);">If import fails, the log status changes to failed.</span></li>
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
                                <div style="font-size:13px; font-weight:500; color:var(--ink);">{{ basename($log->file_name) }}</div>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('upload_type');
            const select = document.getElementById('import_log_id');
            const fileField = document.getElementById('uploadedFileField');
            const noFilesForType = document.getElementById('noFilesForType');
            const fiscalYear = document.getElementById('selectedFiscalYear');
            const month = document.getElementById('selectedMonth');
            const type = document.getElementById('selectedType');
            const status = document.getElementById('selectedStatus');
            const submitButton = document.getElementById('submitImportBtn');

            const filterFiles = () => {
                const selectedType = typeSelect.value;
                let availableCount = 0;

                [...select.options].forEach(option => {
                    if (!option.value) {
                        return;
                    }

                    const matches = option.dataset.uploadType === selectedType;
                    option.hidden = !matches;
                    option.disabled = !matches;
                    availableCount += matches ? 1 : 0;
                });

                fileField.hidden = !selectedType;

                if (select.selectedOptions[0]?.dataset.uploadType !== selectedType) {
                    select.value = '';
                }

                noFilesForType.hidden = !selectedType || availableCount > 0;
                select.disabled = Boolean(selectedType) && availableCount === 0;
                window.CustomSelects?.refresh();
                updateSelection();
            };

            const updateSelection = () => {
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    fiscalYear.textContent = '-';
                    month.textContent = '-';
                    type.textContent = '-';
                    status.textContent = '-';
                    submitButton.disabled = true;
                    submitButton.textContent = 'Import to Database';
                    return;
                }

                fiscalYear.textContent = selectedOption.dataset.fiscalYear || '-';
                month.textContent = selectedOption.dataset.month || '-';
                type.textContent = selectedOption.dataset.type || '-';
                status.textContent = selectedOption.dataset.status || '-';

                submitButton.disabled = false;
                submitButton.textContent = 'Import to Database';
            };

            typeSelect.addEventListener('change', filterFiles);
            select.addEventListener('change', updateSelection);
            filterFiles();
        });
    </script>
</x-app-layout>
