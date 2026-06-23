<x-app-layout>
    <x-slot name="title">Master Data</x-slot>
    <x-slot name="crumbs">Master Data</x-slot>

    <nav class="tabs" data-tabs>
        <a href="#" data-tab="prov" class="active">Provinces</a>
        <a href="#" data-tab="dist">Districts</a>
        <a href="#" data-tab="pt">Policy Types</a>
        <a href="#" data-tab="ct">Complain Types</a>
        <a href="#" data-tab="conf">Configuration</a>
    </nav>

    <div data-tab-panel="prov">
        <div class="flex between center mb-3">
            <h2 style="margin:0; font-size:16px;">Provinces ({{ $provinces->count() }})</h2>
            <a href="{{ route('provinces.create') }}" class="btn btn-primary">+ Add Province</a>
        </div>
        <div class="table-wrap">
            <table class="t">
                <thead><tr><th>ID</th><th>Name</th><th>Districts</th><th>Branches</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($provinces as $province)
                        <tr>
                            <td>{{ $province->province_id }}</td>
                            <td>{{ $province->province_name }}</td>
                            <td>{{ $province->districts_count }}</td>
                            <td class="num">{{ $province->branches_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('provinces.edit', $province->province_id) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('provinces.destroy', $province->province_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this province?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No provinces found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div data-tab-panel="dist" class="hide">
        <div class="flex between center mb-3">
            <h2 style="margin:0; font-size:16px;">Districts</h2>
            <a href="{{ route('districts.create') }}" class="btn btn-primary">+ Add District</a>
        </div>
        <div class="table-wrap">
            <table class="t">
                <thead><tr><th>ID</th><th>District</th><th>Province</th><th>Branches</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($districts as $district)
                        <tr>
                            <td>{{ $district->district_id }}</td>
                            <td>{{ $district->district_name }}</td>
                            <td>{{ $district->province->province_name }}</td>
                            <td class="num">{{ $district->branches_count }}</td>
                            <td class="text-right">
                                <a href="{{ route('districts.edit', $district->district_id) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('districts.destroy', $district->district_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this district?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No districts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div data-tab-panel="pt" class="hide">
        <div class="flex between center mb-3">
            <h2 style="margin:0; font-size:16px;">Policy Types ({{ $policies->count() }})</h2>
            <a href="{{ route('policies.create') }}" class="btn btn-primary">+ Add Policy Type</a>
        </div>
        <div class="table-wrap">
            <table class="t">
                <thead><tr><th>ID</th><th>Policy Type</th><th>Active</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse ($policies as $policy)
                        <tr>
                            <td>{{ $policy->policy_id }}</td>
                            <td>{{ $policy->policy_name }}</td>
                            <td><span class="badge {{ $policy->status === 'active' ? 'success' : 'danger' }}">{{ $policy->status === 'active' ? 'Yes' : 'No' }}</span></td>
                            <td class="text-right"><a href="{{ route('policies.edit', $policy->policy_id) }}" class="btn btn-ghost btn-sm">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No policy types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div data-tab-panel="ct" class="hide">
        <div class="flex between center mb-3">
            <h2 style="margin:0; font-size:16px;">Complain Types ({{ $complainTypes->count() }})</h2>
        </div>
        <div class="table-wrap">
            <table class="t">
                <thead><tr><th>ID</th><th>Name</th></tr></thead>
                <tbody>
                    @forelse ($complainTypes as $complainType)
                        <tr>
                            <td>{{ $complainType->id }}</td>
                            <td>{{ $complainType->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-muted">No complain types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div data-tab-panel="conf" class="hide">
        <div class="card">
            <div class="card-head"><h2>Basic Configuration</h2></div>
            <div class="card-body">
                <div class="grid cols-2">
                    <div class="field"><label>Current Fiscal Year</label><input class="input" value="2082-83"></div>
                    <div class="field"><label>Display Currency</label><select class="select"><option>NPR (Lakhs)</option><option>NPR (Crores)</option></select></div>
                    <div class="field"><label>Public Dashboard Title</label><input class="input" value="PRABHU INSURANCE LIMITED - TRANSPARENCY DASHBOARD"></div>
                    <div class="field"><label>Insurance Products URL</label><input class="input" value="https://prabhuinsurance.com/products"></div>
                    <div class="field"><label>Agents Directory URL</label><input class="input" value="https://prabhuinsurance.com/agents"></div>
                    <div class="field"><label>Surveyors Directory URL</label><input class="input" value="https://prabhuinsurance.com/surveyors"></div>
                </div>
                <button class="btn btn-primary mt-3">Save Configuration</button>
            </div>
        </div>
    </div>
</x-app-layout>
