<x-app-layout>
    <x-slot name="title">Upload Data</x-slot>
    <x-slot name="crumbs">Upload Data</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <form method="POST" action="{{ route('upload.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="card">
                    <div class="card-head"><h2>Step 1 - Select reporting period</h2></div>
                    <div class="card-body">
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
                        <div class="text-muted" style="font-size:13px; margin-top:10px;">Select the fiscal year and Nepali month, then confirm the file upload. Submission date, timestamp, and uploaded by stay automatic.</div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-head"><h2>Step 2 - Upload Excel file</h2><a href="#" class="btn btn-ghost btn-sm" style="border:1px solid var(--line);">Download Template</a></div>
                    <div class="card-body">
                        <label class="dropzone" id="dropzone">
                            <input type="file" name="file" id="uploadFile" accept=".xlsx,.xls,.csv" hidden required>
                            <div class="icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></div>
                            <h3 id="dropzoneTitle">Drop your file here</h3>
                            <p id="dropzoneText">or click to browse - Accepted: .xlsx, .xls, .csv - Max 20MB</p>
                        </label>

                        <div class="card mt-4 hide" id="confirmUploadPanel" style="border:1px solid var(--line); box-shadow:none;">
                            <div class="card-body">
                                <div class="flex between center">
                                    <div>
                                        <strong>Confirm upload</strong>
                                        <div class="text-muted" id="selectedFileName" style="font-size:13px; margin-top:4px;"></div>
                                    </div>
                                    <span class="badge warning">Waiting confirmation</span>
                                </div>
                                <div class="flex gap-3 mt-4">
                                    <button type="button" class="btn btn-primary" id="confirmUploadBtn">Confirm Upload</button>
                                    <button type="button" class="btn btn-outline" id="changeFileBtn">Change File</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card mt-4">
                <div class="card-head">
                    <h2>Step 3 - Open Import Module</h2>
                    <span class="badge {{ $selectedImportLog && $selectedImportLog->status !== 'completed' ? 'warning' : 'success' }}">
                        {{ $selectedImportLog ? ucfirst($selectedImportLog->status) : 'Waiting for upload' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="grid cols-4 mb-4">
                        <div class="kpi"><div class="label">Selected File</div><div class="value" style="font-size:13px; font-weight:500; line-height:1.4; overflow-wrap:anywhere;">{{ $selectedImportLog ? basename($selectedImportLog->file_name) : 'None' }}</div></div>
                        <div class="kpi"><div class="label">Fiscal Year</div><div class="value">{{ $selectedImportLog?->fiscal_year ?? '-' }}</div></div>
                        <div class="kpi"><div class="label">Month</div><div class="value">{{ $selectedImportLog ? ($monthNames[$selectedImportLog->month] ?? $selectedImportLog->month) : '-' }}</div></div>
                        <div class="kpi"><div class="label">Status</div><div class="value" style="font-size:18px;">{{ $selectedImportLog ? ucfirst($selectedImportLog->status) : 'Pending' }}</div></div>
                    </div>

                    @if ($selectedImportLog)
                        <div class="text-muted" style="font-size:13px; margin-bottom:16px;">Uploaded file is saved on the server. Open the import module to select this file and import it into the transactions table.</div>

                        <div class="flex gap-3 mt-4">
                            <a href="{{ route('upload.import-module') }}" class="btn btn-primary">Open Import Module</a>
                            <a href="{{ route('upload.create') }}" class="btn btn-outline">Reset Selection</a>
                            <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="border:1px solid var(--line);">Cancel</a>
                        </div>
                    @else
                        <div class="text-muted" style="font-size:13px;">Upload a file in Step 2 first. After that, open the import module and choose the uploaded file.</div>
                        <div class="flex gap-3 mt-4">
                            <button type="button" class="btn btn-primary" disabled>Open Import Module</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="border:1px solid var(--line);">Cancel</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-head"><h2>Guidelines</h2></div>
                <div class="card-body" style="font-size:13.5px; color: var(--ink-soft); line-height:1.7;">
                    <ul style="list-style:disc outside; padding-left:22px; margin:0; display:grid; gap:6px; color:var(--brand);">
                        <li><span style="color:var(--ink-soft);">Use the provided Excel template only.</span></li>
                        <li><span style="color:var(--ink-soft);">Do not rename or reorder columns.</span></li>
                        <li><span style="color:var(--ink-soft);">Dates must be in YYYY-MM-DD (BS).</span></li>
                        <li><span style="color:var(--ink-soft);">One file per month per fiscal year.</span></li>
                        <li><span style="color:var(--ink-soft);">Re-uploading a month replaces previous data after confirmation.</span></li>
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-head"><h2>Recent Uploads</h2></div>
                <div class="card-body" style="padding:0;">
                    @forelse ($recentUploads as $upload)
                        <div class="val-row"><span class="badge success">OK</span><div><a href="{{ asset('storage/'.$upload->file_name) }}" target="_blank" style="font-size:13px; font-weight:500; color:var(--ink);">{{ basename($upload->file_name) }}</a><div class="text-muted" style="font-size:12px;">FY {{ $upload->fiscal_year }} - {{ $monthNames[$upload->month] ?? $upload->month }} - {{ $upload->created_at?->diffForHumans() }}</div></div></div>
                    @empty
                        <div class="val-row text-muted">No recent uploads.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('uploadFile');
            const dropzone = document.getElementById('dropzone');
            const dropzoneTitle = document.getElementById('dropzoneTitle');
            const dropzoneText = document.getElementById('dropzoneText');
            const confirmPanel = document.getElementById('confirmUploadPanel');
            const selectedFileName = document.getElementById('selectedFileName');
            const confirmButton = document.getElementById('confirmUploadBtn');
            const changeButton = document.getElementById('changeFileBtn');
            const showConfirmation = () => {
                const file = fileInput.files[0];

                if (!file) {
                    return;
                }

                selectedFileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                dropzoneTitle.textContent = file.name;
                dropzoneText.textContent = 'File selected. Confirm upload below to save it on the server.';
                confirmPanel.classList.remove('hide');
            };

            fileInput.addEventListener('change', showConfirmation);

            dropzone.addEventListener('dragover', event => {
                event.preventDefault();
            });

            dropzone.addEventListener('drop', event => {
                event.preventDefault();

                if (event.dataTransfer.files.length) {
                    fileInput.files = event.dataTransfer.files;
                    showConfirmation();
                }
            });

            confirmButton.addEventListener('click', () => {
                if (fileInput.files.length) {
                    form.submit();
                }
            });

            changeButton.addEventListener('click', () => {
                fileInput.value = '';
                confirmPanel.classList.add('hide');
                dropzoneTitle.textContent = 'Drop your file here';
                dropzoneText.textContent = 'or click to browse - Accepted: .xlsx, .xls, .csv - Max 20MB';
            });
        });
    </script>
</x-app-layout>
