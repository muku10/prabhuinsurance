<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="crumbs">Dashboard</x-slot>

    @php
        $statusClasses = [
            'completed' => 'success',
            'processing' => 'warning',
            'pending' => 'info',
            'failed' => 'danger',
        ];
        $user = auth()->user();
    @endphp

    <div class="grid cols-4 mb-6">
        <div class="kpi">
            <div class="label">Latest Upload</div>
            <div class="value">{{ $latestUploadLabel }}</div>
            <div class="text-muted" style="margin-top: 6px; font-size: 13px;">{{ $latestUploadDelta }}</div>
        </div>
        <div class="kpi">
            <div class="label">Total Records</div>
            <div class="value">{{ $totalRecords }}</div>
            <div class="text-muted" style="margin-top: 6px; font-size: 13px;">{{ $totalRecordsDelta }}</div>
        </div>
        <div class="kpi">
            <div class="label">Months Processed</div>
            <div class="value">{{ number_format($monthsProcessed) }}</div>
            <div class="text-muted" style="margin-top: 6px; font-size: 13px;">FY {{ $currentFiscalYear }}</div>
        </div>
        <div class="kpi">
            <div class="label">Active Branches</div>
            <div class="value">{{ number_format($activeBranches) }}</div>
            <div class="text-muted" style="margin-top: 6px; font-size: 13px;">{{ number_format($provinceCount) }} provinces</div>
        </div>
    </div>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Recent Uploads</h2>
                    <a class="btn btn-ghost btn-sm" href="{{ route('import-logs.index') }}">View all -></a>
                </div>
                <div class="table-wrap" style="border:none; border-radius:0;">
                    <table class="t">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File</th>
                                <th>Fiscal Year</th>
                                <th>Month</th>
                                <th>Uploaded</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUploads as $upload)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $upload->upload_type ?? 'irms')) }}</td>
                                    <td>{{ basename($upload->file_name) }}</td>
                                    <td>{{ $upload->fiscal_year }}</td>
                                    <td>{{ $monthNames[$upload->month] ?? $upload->month }}</td>
                                    <td>{{ $upload->created_at->diffForHumans() }}</td>
                                    <td>{{ $upload->date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$upload->status] ?? 'info' }}">
                                            {{ ucfirst($upload->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-muted">No upload history available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h2>Quick Actions</h2></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                <a class="btn btn-primary" href="{{ route('upload.type', 'irms') }}">Upload New File</a>
                <a class="btn btn-outline" href="{{ route('import-logs.index') }}">Upload History</a>
                <a class="btn btn-outline" href="{{ route('master-data.index') }}">Manage Master Data</a>
                <a class="btn btn-outline" href="{{ route('dashboard') }}">Public Dashboard</a>
                <a class="btn btn-outline" href="{{ route('upload.type', 'irms') }}">Download Template</a>
                <div style="border-top:1px solid var(--line); margin:6px 0; padding-top:10px;">
                    <div class="text-muted" style="font-size:13px;">Logged in as</div>
                    <strong>{{ trim(($user->first_name ?? '').' '.($user->last_name ?? '')) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>System Snapshot</h2>
            <span class="badge success">Live</span>
        </div>
        <div class="card-body grid cols-4">
            <div>
                <span class="text-muted">Current Fiscal Year</span><br>
                <strong>{{ $currentFiscalYear }}</strong>
            </div>
            <div>
                <span class="text-muted">Policies</span><br>
                <strong>{{ number_format($policyCount) }}</strong>
            </div>
            <div>
                <span class="text-muted">Pending Uploads</span><br>
                <strong>{{ number_format($pendingUploads) }}</strong>
            </div>
            <div>
                <span class="text-muted">Failed Uploads</span><br>
                <strong>{{ number_format($failedUploads) }}</strong>
            </div>
        </div>
    </div>
</x-app-layout>
