<x-app-layout>
    <x-slot name="title">Add Policy Type</x-slot>
    <x-slot name="crumbs">Master Data · Policy Types · Add</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Add Policy Type</h2>
                    <a href="{{ route('master-data.index') }}" class="btn btn-outline btn-sm">Back to Master Data</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('policies.store') }}">
                        @csrf
                        <div class="field mb-4">
                            <label for="policy_id">Policy ID</label>
                            <input class="input" id="policy_id" type="number" name="policy_id" value="{{ old('policy_id') }}" required min="1" placeholder="Enter a unique policy ID">
                            @error('policy_id')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="parent_id">Parent Policy (optional)</label>
                            <select class="input" id="parent_id" name="parent_id">
                                <option value="">None</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->policy_id }}" @selected(old('parent_id') == $parent->policy_id)>{{ $parent->policy_name }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name">Policy Name</label>
                            <input class="input" id="policy_name" type="text" name="policy_name" value="{{ old('policy_name') }}" required autofocus>
                            @error('policy_name')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name_np">Nepali Name</label>
                            <input class="input" id="policy_name_np" type="text" name="policy_name_np" value="{{ old('policy_name_np') }}">
                            @error('policy_name_np')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="status">Status</label>
                            <select class="input" id="status" name="status" required>
                                <option value="active" @selected(old('status') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Save Policy Type</button>
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
                        <li><span style="color:var(--ink-soft);">Use a clear, descriptive policy name.</span></li>
                        <li><span style="color:var(--ink-soft);">Set a parent to nest this under another type.</span></li>
                        <li><span style="color:var(--ink-soft);">New policies default to active status.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
