<x-app-layout>
    <x-slot name="title">Add District</x-slot>
    <x-slot name="crumbs">Master Data · Districts · Add</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Add District</h2>
                    <a href="{{ route('master-data.index') }}" class="btn btn-outline btn-sm">Back to Master Data</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('districts.store') }}">
                        @csrf
                        <div class="field mb-4">
                            <label for="province_id">Province</label>
                            <select class="input" id="province_id" name="province_id" required>
                                <option value="">Select Province</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->province_id }}" @selected(old('province_id') == $province->province_id)>{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                            @error('province_id')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="district_name">District Name</label>
                            <input class="input" id="district_name" type="text" name="district_name" value="{{ old('district_name') }}" required autofocus>
                            @error('district_name')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Save District</button>
                            <a href="{{ route('master-data.index') }}" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div style="position: sticky; top: 24px; align-self: start;">
            <div class="card">
                <div class="card-head"><h2>Tips</h2></div>
                <div class="card-body" style="font-size:13.5px; color: var(--ink-soft); line-height:1.7;">
                    <ul style="list-style:disc outside; padding-left:22px; margin:0; display:grid; gap:6px; color:var(--brand);">
                        <li><span style="color:var(--ink-soft);">Pick the province this district belongs to.</span></li>
                        <li><span style="color:var(--ink-soft);">Use the official district name as in IRMS.</span></li>
                        <li><span style="color:var(--ink-soft);">You can edit or delete a district later.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
