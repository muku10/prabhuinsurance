<x-app-layout>
    <x-slot name="title">Edit Policy Type</x-slot>
    <x-slot name="crumbs">Master Data · Policy Types · Edit</x-slot>

    <div class="grid cols-3 mb-6">
        <div style="grid-column: span 2;">
            <div class="card">
                <div class="card-head">
                    <h2>Edit Policy Type</h2>
                    <a href="{{ route('master-data.index') }}" class="btn btn-outline btn-sm">Back to Master Data</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('policies.update', $policy->policy_id) }}">
                        @csrf
                        @method('PUT')
                        <div class="field mb-4">
                            <label for="policy_id">Policy ID</label>
                            <input class="input" id="policy_id" type="number" name="policy_id" value="{{ old('policy_id', $policy->policy_id) }}" required min="1">
                            @error('policy_id')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="parent_id">Parent Policy (optional)</label>
                            <select class="input" id="parent_id" name="parent_id">
                                <option value="">None</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->policy_id }}" @selected(old('parent_id', $policy->parent_id) == $parent->policy_id)>{{ $parent->policy_name }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name">Policy Name</label>
                            <input class="input" id="policy_name" type="text" name="policy_name" value="{{ old('policy_name', $policy->policy_name) }}" required autofocus>
                            @error('policy_name')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name_np">Nepali Name</label>
                            <input class="input" id="policy_name_np" type="text" name="policy_name_np" value="{{ old('policy_name_np', $policy->policy_name_np) }}">
                            @error('policy_name_np')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="field mb-4">
                            <label for="status">Status</label>
                            <select class="input" id="status" name="status" required>
                                <option value="active" @selected(old('status', $policy->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $policy->status) === 'inactive')>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-muted" style="color:#DC2626; margin-top:6px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">Update Policy Type</button>
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
                        <li><span style="color:var(--ink-soft);">Parent policy groups related policy types together.</span></li>
                        <li><span style="color:var(--ink-soft);">Inactive policies are hidden from new records.</span></li>
                        <li><span style="color:var(--ink-soft);">Nepali name is optional but recommended.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
