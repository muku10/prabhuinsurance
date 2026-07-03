<x-app-layout>
    <x-slot name="title">Upload Data</x-slot>
    <x-slot name="crumbs">Upload Data</x-slot>

    @php
        $uploadCategories = [
            ['key' => 'irms', 'title' => 'IRMS', 'description' => 'Upload the core IRMS workbook for the selected period.', 'extensions' => '.xlsx, .xls, .csv'],
            ['key' => 'outstanding_claim', 'title' => 'Outstanding Claim', 'description' => 'Attach the outstanding claim file separately.', 'extensions' => '.xlsx, .xls, .csv, .pdf'],
            ['key' => 'paid_claim', 'title' => 'Paid Claim', 'description' => 'Attach the paid claim file separately.', 'extensions' => '.xlsx, .xls, .csv, .pdf'],
            ['key' => 'withdrawal_claim', 'title' => 'Withdrawal Claim', 'description' => 'Attach the withdrawal claim file separately.', 'extensions' => '.xlsx, .xls, .csv, .pdf'],
            ['key' => 'intimation_claim', 'title' => 'Intimation Claim', 'description' => 'Attach the intimation claim file separately.', 'extensions' => '.xlsx, .xls, .csv, .pdf'],
        ];

        $uploadTypeLabels = [
            'irms' => 'IRMS',
            'outstanding_claim' => 'Outstanding Claim',
            'paid_claim' => 'Paid Claim',
            'withdrawal_claim' => 'Withdrawal Claim',
            'intimation_claim' => 'Intimation Claim',
        ];

        $recentUploads = \App\Models\ImportLog::latest('date')->latest('id')->limit(8)->get();
    @endphp

    <div class="card mb-4" style="border-top:4px solid var(--danger);">
        <div class="card-body" style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between;">
            <div>
                <div class="text-muted" style="font-size:13px; letter-spacing:.08em; text-transform:uppercase; font-weight:700; color:var(--danger);">Upload workspace</div>
                <h2 style="margin:6px 0 8px; font-size:26px; color:var(--ink);">Choose an upload type</h2>
                <div class="text-muted" style="max-width:760px; font-size:14px; line-height:1.7;">Each upload type has its own dedicated page. Pick a type below to upload a single file for that category. Every uploaded file is stored in its own folder and saved as its own database record.</div>
            </div>
            <div class="kpi" style="min-width:170px;">
                <div class="label">Upload types</div>
                <div class="value">{{ count($uploadCategories) }}</div>
                <div class="text-muted" style="font-size:13px; margin-top:4px;">Separate pages</div>
            </div>
        </div>
    </div>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Select upload type</h2>
                    <span class="badge info">{{ count($uploadCategories) }} options</span>
                </div>
                <div class="card-body">
                    <div class="grid cols-2" style="gap:16px;">
                        @foreach ($uploadCategories as $category)
                            <a href="{{ route('upload.type', $category['key']) }}" class="card" data-upload-card="{{ $category['key'] }}" style="border:1px solid var(--line); box-shadow:none; background:#fff; border-top:3px solid var(--danger); text-decoration:none; color:inherit; transition:box-shadow .15s ease, transform .15s ease;" onmouseover="this.style.boxShadow='0 6px 18px rgba(0,0,0,.08)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='none'; this.style.transform='none';">
                                <div class="card-body" style="display:grid; gap:14px;">
                                    <div class="flex between center" style="gap:12px; align-items:flex-start;">
                                        <div>
                                            <div style="font-size:18px; font-weight:700; color:var(--ink);">{{ $category['title'] }}</div>
                                            <div class="text-muted" style="font-size:13px; margin-top:4px; line-height:1.6;">{{ $category['description'] }}</div>
                                        </div>
                                        <span class="badge info">Open</span>
                                    </div>

                                    <div class="flex between center" style="gap:12px;">
                                        <div class="text-muted" style="font-size:13px;">Accepted: {{ $category['extensions'] }}</div>
                                        <span class="btn btn-ghost btn-sm" style="border:1px solid var(--line);">Upload &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-head">
                    <h2>Import to Database</h2>
                    <span class="badge success">Same action for all</span>
                </div>
                <div class="card-body">
                    <div class="text-muted" style="font-size:13px;">Upload a file from any type page first. Once it is stored, the database import module will be ready.</div>
                    <div class="flex gap-3 mt-4">
                        <a href="{{ route('upload.import-module') }}" class="btn btn-primary">Open Import Module</a>
                        <a href="{{ route('upload.database-history') }}" class="btn btn-outline">View Database History</a>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
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
                                    <td>{{ $uploadTypeLabels[$upload->upload_type ?? 'irms'] ?? ucfirst(str_replace('_', ' ', $upload->upload_type ?? 'irms')) }}</td>
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
                        <li><span style="color:var(--ink-soft);">Each upload type has its own dedicated page.</span></li>
                        <li><span style="color:var(--ink-soft);">Each upload type is stored separately in its own folder.</span></li>
                        <li><span style="color:var(--ink-soft);">Upload history stays separate from the database import history.</span></li>
                        <li><span style="color:var(--ink-soft);">Any uploaded file can open the import module from the history list.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
