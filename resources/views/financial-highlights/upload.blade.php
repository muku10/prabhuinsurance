<x-app-layout>
    <x-slot name="title">Financial Highlights Upload</x-slot>
    <x-slot name="crumbs">Financial Highlights / Upload</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column:span 2;">
            <div class="card">
                <div class="card-head"><h2>Upload Financial Highlights</h2><a href="{{ route('financial-highlights.template') }}" class="btn btn-outline">Download Excel Template</a></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('financial-highlights.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-row">
                            <div class="field">
                                <label for="fiscal_year">Financial Year</label>
                                <select class="input" id="fiscal_year" name="fiscal_year" required>
                                    @foreach($fiscalYears as $year)<option value="{{ $year }}" @selected(old('fiscal_year') === $year)>{{ $year }}</option>@endforeach
                                </select>
                                @error('fiscal_year')<div style="color:#DC2626;font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                            </div>
                            <div class="field">
                                <label for="quarter">Quarter</label>
                                <select class="input" id="quarter" name="quarter" required>
                                    @foreach($quarters as $number => $label)<option value="{{ $number }}" @selected((int)old('quarter') === $number)>{{ $label }}</option>@endforeach
                                </select>
                                @error('quarter')<div style="color:#DC2626;font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="field mt-4">
                            <label for="file">Excel File</label>
                            <input class="input" id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required>
                            @error('file')<div style="color:#DC2626;font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                            <div class="text-muted" style="font-size:12px;margin-top:6px;">Accepted formats: XLSX, XLS, CSV. Maximum size: 20 MB.</div>
                        </div>
                        <div class="flex gap-3 mt-4"><button class="btn btn-primary">Save Upload</button><a href="{{ route('financial-highlights.import') }}" class="btn btn-outline">Import &amp; History</a></div>
                    </form>
                </div>
            </div>
        </div>
        @php
            $requiredHeadings = [
                ['name' => 'Solvency Ratio', 'unit' => 'x'],
                ['name' => 'Return on Equity', 'unit' => '%'],
                ['name' => 'Earnings per Share', 'unit' => 'NPR'],
                ['name' => 'Net Worth', 'unit' => 'NPR'],
                ['name' => 'Net Profit Margin', 'unit' => '%'],
                ['name' => 'Liquidity Ratio', 'unit' => 'Ratio'],
                ['name' => 'Investment Yield', 'unit' => '%'],
            ];
        @endphp
        <div class="card excel-heading-card">
            <div class="card-head">
                <div>
                    <h2>Required Excel Headings</h2>
                    <div class="text-muted excel-heading-subtitle">Keep these column names unchanged</div>
                </div>
                <span class="badge info">7 columns</span>
            </div>
            <div class="excel-heading-list">
                @foreach ($requiredHeadings as $heading)
                    <div class="excel-heading-row">
                        <span class="excel-heading-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="excel-heading-copy">
                            <strong>{{ $heading['name'] }}</strong>
                            <small>Excel column {{ $loop->iteration }}</small>
                        </div>
                        <span class="excel-heading-unit">{{ $heading['unit'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="excel-heading-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg>
                Use the downloadable template to avoid heading validation errors.
            </div>
        </div>
    </div>
    <div class="card"><div class="card-head"><h2>Recent Uploads</h2></div><div class="table-wrap" style="border:0;"><table class="t"><thead><tr><th>File</th><th>Financial Year</th><th>Quarter</th><th>Status</th><th>Uploaded By</th><th></th></tr></thead><tbody>
        @forelse($recentImports as $item)<tr><td>{{ $item->original_file_name }}</td><td>{{ $item->fiscal_year }}</td><td>{{ $quarters[$item->quarter] }}</td><td><span class="badge {{ $item->status === 'completed' ? 'success' : ($item->status === 'failed' ? 'danger' : 'info') }}">{{ ucfirst($item->status) }}</span></td><td>{{ $item->user?->full_name ?? $item->user?->email }}</td><td><a class="btn btn-ghost btn-sm" href="{{ route('financial-highlights.import', ['import_id'=>$item->id]) }}">Open</a></td></tr>
        @empty<tr><td colspan="6" class="text-muted">No financial highlight uploads yet.</td></tr>@endforelse
    </tbody></table></div></div>
</x-app-layout>
