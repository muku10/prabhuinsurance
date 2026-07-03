<x-app-layout>
    <x-slot name="title">Add Province</x-slot>
    <x-slot name="crumbs">Master Data · Provinces · Add</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Add Province</h2>
                    <a href="{{ route('master-data.index') }}" class="btn btn-outline btn-sm">Back to Master Data</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('provinces.store') }}">
                        @csrf
                        <div class="field mb-4">
                            <label for="province_name">Province Name</label>
                            <input class="input" id="province_name" type="text" name="province_name" value="{{ old('province_name') }}" required autofocus>
                            @error('province_name')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Save Province</button>
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
                        <li><span style="color:var(--ink-soft);">Use the official province name as recorded in IRMS.</span></li>
                        <li><span style="color:var(--ink-soft);">Provinces are referenced by districts and branches.</span></li>
                        <li><span style="color:var(--ink-soft);">You can edit or delete a province later.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
