<x-app-layout>
    <x-slot name="title">Upload Data</x-slot>
    <x-slot name="crumbs">Upload Data</x-slot>

    @php
        $uploadCategories = [
            ['key' => 'premium', 'title' => 'Premium', 'description' => 'Upload the core Premium workbook for the selected period.'],
            ['key' => 'outstanding_claim', 'title' => 'Outstanding Claim', 'description' => 'Attach the outstanding claim file separately.'],
            ['key' => 'paid_claim', 'title' => 'Paid Claim', 'description' => 'Attach the paid claim file separately.'],
            ['key' => 'withdrawal_claim', 'title' => 'Withdrawal Claim', 'description' => 'Attach the withdrawal claim file separately.'],
            ['key' => 'intimation_claim', 'title' => 'Intimation Claim', 'description' => 'Attach the intimation claim file separately.'],
            ['key' => 'complain', 'title' => 'Grievance', 'description' => 'Attach the grievance file separately.'],
        ];

        $uploadTypeLabels = [
            'premium' => 'Premium',
            'outstanding_claim' => 'Outstanding Claim',
            'paid_claim' => 'Paid Claim',
            'withdrawal_claim' => 'Withdrawal Claim',
            'intimation_claim' => 'Intimation Claim',
            'complain' => 'Grievance',
        ];
    @endphp

    <form method="POST" action="{{ route('upload.store') }}" enctype="multipart/form-data" id="uploadForm">
        @csrf

        {{-- Page header --}}
        <div class="card mb-6" style="border-top:4px solid var(--brand);">
            <div class="card-body" style="display:flex; flex-wrap:wrap; gap:20px; align-items:center; justify-content:space-between;">
                <div>
                    <div class="text-muted" style="font-size:12px; letter-spacing:.1em; text-transform:uppercase; font-weight:800; color:var(--brand);">Upload workspace</div>
                    <h2 style="margin:8px 0 6px; font-size:24px; color:var(--ink); font-weight:800;">Separate file uploads, one record per file</h2>
                    <div class="text-muted" style="max-width:720px; font-size:13.5px; line-height:1.7;">Each file is stored in its own folder and saved as its own database record. Upload one or many files for the selected reporting period.</div>
                </div>
                <div class="kpi" style="min-width:160px;">
                    <div class="label">Upload slots</div>
                    <div class="value">{{ count($uploadCategories) }}</div>
                    <div class="text-muted" style="font-size:12px; margin-top:4px;">Backend enabled</div>
                </div>
            </div>
        </div>

        <div class="grid cols-3 mb-6">
            {{-- Main column --}}
            <div style="grid-column: span 2;">

                {{-- Reporting period --}}
                <div class="card">
                    <div class="card-head">
                        <h2>Reporting period</h2>
                        <span class="badge info">Required</span>
                    </div>
                    <div class="card-body">
                        @if ($errors->has('upload_files'))
                            <div style="border:1px solid #FCA5A5; background:#FEF2F2; color:#991B1B; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px;">{{ $errors->first('upload_files') }}</div>
                        @endif
                        <div class="input-row">
                            <div class="field">
                                <label for="fiscal_year">Fiscal Year</label>
                                <select class="input" name="fiscal_year" id="fiscal_year" required>
                                    @foreach ($fiscalYearOptions as $fiscalYearOption)
                                        <option value="{{ $fiscalYearOption }}" @selected(old('fiscal_year', $currentPeriod['fiscal_year']) === $fiscalYearOption)>{{ $fiscalYearOption }}</option>
                                    @endforeach
                                </select>
                                @error('fiscal_year')
                                    <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label for="month">Month</label>
                                <select class="input" name="month" id="month" required>
                                    @foreach ($monthNames as $monthNumber => $monthLabel)
                                        <option value="{{ $monthNumber }}" @selected((int) old('month', $currentPeriod['month']) === $monthNumber)>{{ $monthLabel }}</option>
                                    @endforeach
                                </select>
                                @error('month')
                                    <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Submission Date</label>
                                <input class="input" type="text" value="{{ $submissionDateBs }}" readonly>
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:13px; margin-top:10px;">Pick the fiscal year and Nepali month once, then attach whichever files are ready for that period.</div>
                    </div>
                </div>

                {{-- Upload files --}}
                <div class="card mt-4">
                    <div class="card-head">
                        <h2>Upload files separately</h2>
                        <!-- <span class="badge warning">UI + backend</span> -->
                    </div>
                    <div class="card-body">
                        <div class="grid" style="grid-template-columns: 1fr; gap:16px;">
                            @foreach ($uploadCategories as $category)
                                <div class="card" data-upload-card="{{ $category['key'] }}" style="border:1px solid var(--line); box-shadow:none; background:#fff; border-left:4px solid var(--brand);">
                                    <div class="card-body" style="display:grid; gap:14px;">
                                        <div class="flex between center" style="gap:12px; align-items:flex-start;">
                                            <div>
                                                <div style="font-size:17px; font-weight:800; color:var(--ink);">{{ $category['title'] }}</div>
                                                <div class="text-muted" style="font-size:13px; margin-top:4px; line-height:1.6;">{{ $category['description'] }}</div>
                                            </div>
                                            <div class="flex gap-2 center">
                                                @if ($category['key'] === 'complain')
                                                    <a href="{{ route('grievances.template') }}" class="btn btn-outline btn-sm" onclick="event.stopPropagation()">Download Template</a>
                                                @endif
                                                <span class="badge info" data-upload-badge>Pending</span>
                                            </div>
                                        </div>

                                        @if ($category['key'] === 'complain')
                                            <div style="border:1px solid var(--line); border-radius:10px; background:#F8FAFC; padding:12px 14px;">
                                                <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); margin-bottom:8px;">Required template headings</div>
                                                <div class="flex" style="flex-wrap:wrap; gap:7px;">
                                                    @foreach (['Grievance Type ID', 'Received Num', 'Resolved Num', 'Average Resolution Time (Days)'] as $heading)
                                                        <span class="badge info">{{ $heading }}</span>
                                                    @endforeach
                                                </div>
                                                <div class="text-muted" style="font-size:11.5px; margin-top:9px; line-height:1.5;">Enter the overall average resolution time once in the first data row. Pending and the overall resolution rate are calculated automatically.</div>
                                            </div>
                                        @endif

                                        @php
                                            $excelOnly = in_array($category['key'], ['premium', 'complain'], true);
                                            $acceptTypes = $excelOnly ? '.xlsx,.xls,.csv' : '.xlsx,.xls,.csv,.pdf';
                                        @endphp
                                        <label class="dropzone" data-dropzone data-target="{{ $category['key'] }}" style="min-height:150px; border-style:dashed; background:#fafafa;">
                                            <input type="file" name="{{ $category['key'] }}_file" id="{{ $category['key'] }}_file" data-upload-input data-target="{{ $category['key'] }}" accept="{{ $acceptTypes }}" hidden>
                                            <div class="icon" aria-hidden="true">
                                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="17 8 12 3 7 8" />
                                                    <line x1="12" y1="3" x2="12" y2="15" />
                                                </svg>
                                            </div>
                                            <h3 id="{{ $category['key'] }}_title" style="margin:0;">Drop {{ $category['title'] }} file here</h3>
                                            <p id="{{ $category['key'] }}_text" style="margin:0;">or click to browse - Accepted: {{ $excelOnly ? '.xlsx, .xls, .csv' : '.xlsx, .xls, .csv, .pdf' }}</p>
                                        </label>

                                        <div class="flex between center" style="gap:12px;">
                                            <div class="text-muted" id="{{ $category['key'] }}_meta" style="font-size:13px; overflow-wrap:anywhere;">No file selected</div>
                                            <button type="button" class="btn btn-ghost btn-sm" data-clear-upload="{{ $category['key'] }}" style="border:1px solid var(--line);">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">Save Uploads</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div style="position: sticky; top: 24px; align-self: start; max-height: calc(100vh - 48px); overflow-y: auto;">
                <div class="card">
                    <div class="card-head">
                        <h2>Selected files</h2>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <table class="t">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>File</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uploadCategories as $category)
                                    <tr>
                                        <td>{{ $category['title'] }}</td>
                                        <td class="text-muted" data-selected-file="{{ $category['key'] }}">No file selected</td>
                                        <td><span class="badge info" data-selected-status="{{ $category['key'] }}">Pending</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-head">
                        <h2>Saved uploads</h2>
                    </div>
                    <div class="table-wrap" style="border:none; border-radius:0;">
                        <table class="t">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>File</th>
                                    <th>Month</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentUploads as $upload)
                                    <tr>
                                        <td>{{ $uploadTypeLabels[$upload->upload_type ?? 'premium'] ?? ucfirst(str_replace('_', ' ', $upload->upload_type ?? 'premium')) }}</td>
                                        <td>
                                            <a href="{{ asset('storage/'.$upload->file_name) }}" target="_blank" style="font-weight:500; color:var(--ink);">{{ basename($upload->file_name) }}</a>
                                            <div class="text-muted" style="font-size:12px; margin-top:2px;">FY {{ $upload->fiscal_year }}</div>
                                        </td>
                                        <td>{{ $monthNames[$upload->month] ?? $upload->month }}</td>
                                        <td><span class="badge {{ $upload->status === 'completed' ? 'success' : ($upload->status === 'failed' ? 'danger' : 'info') }}">{{ ucfirst($upload->status) }}</span></td>
                                        <td style="text-align:right; white-space:nowrap;">
                                            <a href="{{ route('upload.import-module', ['import_log_id' => $upload->id]) }}" class="btn btn-ghost btn-sm" style="border:1px solid var(--line);">Open Import</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">No uploads yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-head">
                        <h2>Guidelines</h2>
                    </div>
                    <div class="card-body" style="font-size:13.5px; color: var(--ink-soft); line-height:1.7;">
                        <ul style="list-style:disc outside; padding-left:22px; margin:0; display:grid; gap:6px; color:var(--brand);">
                            <li><span style="color:var(--ink-soft);">Each upload type is stored separately in its own folder.</span></li>
                            <li><span style="color:var(--ink-soft);">Upload history stays separate from the database import history.</span></li>
                            <li><span style="color:var(--ink-soft);">Any uploaded file can open the import module from the history list.</span></li>
                            <li><span style="color:var(--ink-soft);">You can upload one or many files at the same time.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const formatSize = bytes => `${(bytes / 1024 / 1024).toFixed(2)} MB`;

                const updateCard = (key, file) => {
                    const title = document.getElementById(`${key}_title`);
                    const text = document.getElementById(`${key}_text`);
                    const meta = document.getElementById(`${key}_meta`);
                    const card = document.querySelector(`[data-upload-card="${key}"]`);
                    const badge = card?.querySelector('[data-upload-badge]');
                    const selectedFile = document.querySelector(`[data-selected-file="${key}"]`);
                    const selectedStatus = document.querySelector(`[data-selected-status="${key}"]`);

                    if (!title || !text || !meta || !badge || !selectedFile || !selectedStatus) {
                        return;
                    }

                    const defaultTitle = title.dataset.defaultTitle || title.textContent.replace(/^Drop /, '').replace(/ file here$/, '');

                    if (!file) {
                        title.textContent = `Drop ${defaultTitle} file here`;
                        text.textContent = 'or click to browse - Accepted: .xlsx, .xls, .csv, .pdf';
                        meta.textContent = 'No file selected';
                        badge.textContent = 'Pending';
                        badge.className = 'badge info';
                        selectedFile.textContent = 'No file selected';
                        selectedStatus.textContent = 'Pending';
                        selectedStatus.className = 'badge info';
                        return;
                    }

                    title.textContent = file.name;
                    text.textContent = 'File selected. You can replace it or clear it using the controls below.';
                    meta.textContent = `${file.name} • ${formatSize(file.size)}`;
                    badge.textContent = 'Ready';
                    badge.className = 'badge success';
                    selectedFile.textContent = `${file.name} • ${formatSize(file.size)}`;
                    selectedStatus.textContent = 'Ready';
                    selectedStatus.className = 'badge success';
                };

                document.querySelectorAll('[data-upload-input]').forEach(input => {
                    const key = input.dataset.target;
                    const dropzone = document.querySelector(`[data-dropzone][data-target="${key}"]`);
                    const clearButton = document.querySelector(`[data-clear-upload="${key}"]`);
                    const title = document.getElementById(`${key}_title`);

                    if (title) {
                        title.dataset.defaultTitle = title.textContent.replace(/^Drop /, '').replace(/ file here$/, '');
                    }

                    input.addEventListener('change', () => {
                        updateCard(key, input.files[0] ?? null);
                    });

                    if (dropzone) {
                        dropzone.addEventListener('dragover', event => {
                            event.preventDefault();
                        });

                        dropzone.addEventListener('drop', event => {
                            event.preventDefault();

                            if (!event.dataTransfer.files.length) {
                                return;
                            }

                            input.files = event.dataTransfer.files;
                            updateCard(key, input.files[0] ?? null);
                        });
                    }

                    if (clearButton) {
                        clearButton.addEventListener('click', () => {
                            input.value = '';
                            updateCard(key, null);
                        });
                    }
                });
            });
        </script>
    </form>
</x-app-layout>
