<x-app-layout>
    <x-slot name="title">Edit Province</x-slot>
    <x-slot name="crumbs">Master Data · Provinces · Edit</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Edit Province</h2>
                    <a href="{{ route('master-data.index') }}" class="btn btn-outline btn-sm">Back to Master Data</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('provinces.update', $province->province_id) }}">
                        @csrf
                        @method('PUT')
                        <div class="field mb-4">
                            <label for="province_name">Province Name</label>
                            <input class="input" id="province_name" type="text" name="province_name" value="{{ old('province_name', $province->province_name) }}" required autofocus>
                            @error('province_name')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Update Province</button>
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
                        <li><span style="color:var(--ink-soft);">Province names are used across districts and branches.</span></li>
                        <li><span style="color:var(--ink-soft);">Renaming a province updates it everywhere it appears.</span></li>
                        <li><span style="color:var(--ink-soft);">Deleting a province may affect linked districts.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
