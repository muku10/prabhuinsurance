<x-app-layout>
    <x-slot name="title">Master Data</x-slot>
    <x-slot name="crumbs">Master Data</x-slot>

    <div x-data="masterData()">
        <nav class="tabs" data-tabs>
            <a href="#" data-tab="prov" class="active">Provinces</a>
            <a href="#" data-tab="dist">Districts</a>
            <a href="#" data-tab="pt">Policy Types</a>
            <a href="#" data-tab="br">Branches</a>
            <a href="#" data-tab="np">Agents &amp; Surveyors</a>
            <a href="#" data-tab="ct">Grievance Types</a>
            <a href="#" data-tab="conf">Configuration</a>
        </nav>

        {{-- ===================== PROVINCES ===================== --}}
        <div data-tab-panel="prov">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Provinces ({{ $provinces->count() }})</h2>
                <button type="button" class="btn btn-primary" @click="addProvince()">+ Add Province</button>
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
                                    <button type="button" class="btn btn-ghost btn-sm"
                                        @click='openProvince({{ $province->province_id }}, {{ json_encode($province->province_name) }}, {{ json_encode($province->code ?? '') }})'>Edit</button>
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

        {{-- ===================== DISTRICTS ===================== --}}
        <div data-tab-panel="dist" class="hide">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Districts</h2>
                <button type="button" class="btn btn-primary" @click="addDistrict()">+ Add District</button>
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
                                    <button type="button" class="btn btn-ghost btn-sm"
                                        @click='openDistrict({{ $district->district_id }}, {{ $district->province_id }}, {{ json_encode($district->district_name) }}, {{ json_encode($district->code ?? '') }})'>Edit</button>
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

        {{-- ===================== POLICY TYPES ===================== --}}
        <div data-tab-panel="pt" class="hide">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Policy Types ({{ $policies->count() }})</h2>
                <button type="button" class="btn btn-primary" @click="addPolicy()">+ Add Policy Type</button>
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
                                <td class="text-right">
                                    <button type="button" class="btn btn-ghost btn-sm"
                                        @click='openPolicy({{ $policy->policy_id }}, {{ (int)($policy->parent_id ?? 0) }}, {{ json_encode($policy->policy_name) }}, {{ json_encode($policy->policy_name_np ?? '') }}, {{ json_encode($policy->status) }}, {{ json_encode($policy->code ?? '') }})'>Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No policy types found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- ===================== BRANCHES ===================== --}}
        <div data-tab-panel="br" class="hide">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Branches ({{ $branches->count() }})</h2>
                <button type="button" class="btn btn-primary" @click="addBranch()">+ Add Branch</button>
            </div>
            <div class="table-wrap">
                <table class="t">
                    <thead><tr><th>Code</th><th>Ext Code</th><th>Branch</th><th>Province</th><th>District</th><th>Period</th><th>Status</th><th>Inactive Period</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr>
                                <td>{{ $branch->branch_code }}</td>
                                <td>{{ $branch->ext_branch_code }}</td>
                                <td>{{ $branch->branch_name }}</td>
                                <td>{{ $branch->province->province_name }}</td>
                                <td>{{ $branch->district->district_name }}</td>
                                <td>{{ $branch->fiscal_year ?? '-' }} {{ $monthNames[$branch->month] ?? '' }}</td>
                                <td><span class="badge {{ $branch->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($branch->status) }}</span></td>
                                <td>{{ $branch->inactive_fiscal_year ? $branch->inactive_fiscal_year.' '.($monthNames[$branch->inactive_month] ?? '') : '-' }}</td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-ghost btn-sm"
                                        @click='openBranch({{ $branch->id }}, {{ (int) $branch->branch_code }}, {{ json_encode($branch->ext_branch_code) }}, {{ json_encode($branch->branch_name) }}, {{ (int) $branch->province_id }}, {{ (int) $branch->district_id }}, {{ json_encode($branch->fiscal_year ?? '') }}, {{ (int) ($branch->month ?? 0) }}, {{ (int) ($branch->local_level ?? 0) }}, {{ json_encode($branch->address ?? '') }}, {{ json_encode($branch->display_name ?? '') }}, {{ json_encode($branch->status) }}, {{ json_encode($branch->inactive_fiscal_year ?? '') }}, {{ (int) ($branch->inactive_month ?? 0) }})'>Edit</button>
                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this branch?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="_redirect" value="master-data">
                                        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-muted">No branches found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================== AGENTS & SURVEYORS ===================== --}}
        <div data-tab-panel="np" class="hide">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Agent &amp; Surveyor Updates ({{ $networkPersonnel->count() }})</h2>
                <button type="button" class="btn btn-primary" @click="addPersonnel()">+ Add Period Update</button>
            </div>
            <div class="table-wrap">
                <table class="t">
                    <thead><tr><th>Type</th><th>Fiscal Year</th><th>Month</th><th>Number</th><th>Updated</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($networkPersonnel as $personnel)
                            <tr>
                                <td>{{ $personnel->type === 'agent' ? 'Licensed Agents' : 'Surveyors' }}</td>
                                <td>{{ $personnel->fiscal_year }}</td>
                                <td>{{ $monthNames[$personnel->month] ?? $personnel->month }}</td>
                                <td class="num">{{ number_format($personnel->number) }}</td>
                                <td>{{ $personnel->updated_at?->format('d M Y') ?? '-' }}</td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-ghost btn-sm"
                                        @click='openPersonnel({{ $personnel->id }}, {{ json_encode($personnel->type) }}, {{ json_encode($personnel->fiscal_year) }}, {{ (int) $personnel->month }}, {{ (int) $personnel->number }})'>Edit</button>
                                    <form action="{{ route('network-personnel.destroy', $personnel->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this period update?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No agent or surveyor updates found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================== COMPLAIN TYPES ===================== --}}
        <div data-tab-panel="ct" class="hide">
            <div class="flex between center mb-3">
                <h2 style="margin:0; font-size:16px;">Grievance Types ({{ $complainTypes->count() }})</h2>
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
                            <tr><td colspan="2" class="text-muted">No grievance types found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================== CONFIGURATION ===================== --}}
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

        {{-- ============== PROVINCE MODAL (Add / Edit) ============== --}}
        <div class="modal-overlay" x-show="modals.province" x-cloak x-transition.opacity @keydown.escape.window="closeAll()" @click.self="closeAll()">
            <div class="modal-box" x-transition.scale.origin.center>
                <div class="modal-head">
                    <h3 x-text="provinceForm.mode === 'create' ? 'Add Province' : 'Edit Province'">Edit Province</h3>
                    <button type="button" class="modal-close" @click="closeAll()">&times;</button>
                </div>
                <form :action="provinceForm.action" method="POST">
                    @csrf
                    <template x-if="provinceForm.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-body">
                        <div class="field mb-4">
                            <label for="province_name">Province Name</label>
                            <input class="input" id="province_name" type="text" name="province_name" x-model="provinceForm.name" required>
                        </div>
                        <div class="field">
                            <label for="province_code">Code</label>
                            <input class="input" id="province_code" type="text" name="code" x-model="provinceForm.code" maxlength="10">
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-outline" @click="closeAll()">Cancel</button>
                        <button type="submit" class="btn btn-primary" x-text="provinceForm.mode === 'create' ? 'Save Province' : 'Update Province'">Update Province</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============== DISTRICT MODAL (Add / Edit) ============== --}}
        <div class="modal-overlay" x-show="modals.district" x-cloak x-transition.opacity @keydown.escape.window="closeAll()" @click.self="closeAll()">
            <div class="modal-box" x-transition.scale.origin.center>
                <div class="modal-head">
                    <h3 x-text="districtForm.mode === 'create' ? 'Add District' : 'Edit District'">Edit District</h3>
                    <button type="button" class="modal-close" @click="closeAll()">&times;</button>
                </div>
                <form :action="districtForm.action" method="POST">
                    @csrf
                    <template x-if="districtForm.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-body">
                        <div class="field mb-4">
                            <label for="province_id">Province</label>
                            <select class="input" id="province_id" name="province_id" x-model="districtForm.provinceId" required>
                                <option value="">Select Province</option>
                                @foreach ($allProvinces as $province)
                                    <option value="{{ $province->province_id }}">{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="district_name">District Name</label>
                            <input class="input" id="district_name" type="text" name="district_name" x-model="districtForm.name" required>
                        </div>
                        <div class="field">
                            <label for="district_code">Code</label>
                            <input class="input" id="district_code" type="text" name="code" x-model="districtForm.code" maxlength="10">
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-outline" @click="closeAll()">Cancel</button>
                        <button type="submit" class="btn btn-primary" x-text="districtForm.mode === 'create' ? 'Save District' : 'Update District'">Update District</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============== POLICY MODAL (Add / Edit) ============== --}}
        <div class="modal-overlay" x-show="modals.policy" x-cloak x-transition.opacity @keydown.escape.window="closeAll()" @click.self="closeAll()">
            <div class="modal-box" x-transition.scale.origin.center>
                <div class="modal-head">
                    <h3 x-text="policyForm.mode === 'create' ? 'Add Policy Type' : 'Edit Policy Type'">Edit Policy Type</h3>
                    <button type="button" class="modal-close" @click="closeAll()">&times;</button>
                </div>
                <form :action="policyForm.action" method="POST">
                    @csrf
                    <template x-if="policyForm.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-body">
                        <div class="field mb-4">
                            <label for="policy_id">Policy ID</label>
                            <input class="input" id="policy_id" type="number" name="policy_id" x-model.number="policyForm.policyId" required min="1">
                        </div>
                        <div class="field mb-4">
                            <label for="parent_id">Parent Policy (optional)</label>
                            <select class="input" id="parent_id" name="parent_id" x-model="policyForm.parentId">
                                <option value="">None</option>
                                @foreach ($parentPolicies as $parent)
                                    <option value="{{ $parent->policy_id }}">{{ $parent->policy_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name">Policy Name</label>
                            <input class="input" id="policy_name" type="text" name="policy_name" x-model="policyForm.name" required>
                        </div>
                        <div class="field mb-4">
                            <label for="policy_name_np">Nepali Name</label>
                            <input class="input" id="policy_name_np" type="text" name="policy_name_np" x-model="policyForm.nameNp">
                        </div>
                        <div class="field mb-4">
                            <label for="policy_code">Code</label>
                            <input class="input" id="policy_code" type="text" name="code" x-model="policyForm.code" maxlength="10">
                        </div>
                        <div class="field">
                            <label for="status">Status</label>
                            <select class="input" id="status" name="status" x-model="policyForm.status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-outline" @click="closeAll()">Cancel</button>
                        <button type="submit" class="btn btn-primary" x-text="policyForm.mode === 'create' ? 'Save Policy Type' : 'Update Policy Type'">Update Policy Type</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============== BRANCH MODAL (Add / Edit) ============== --}}
        <div class="modal-overlay" x-show="modals.branch" x-cloak x-transition.opacity @keydown.escape.window="closeAll()" @click.self="closeAll()">
            <div class="modal-box" x-transition.scale.origin.center>
                <div class="modal-head">
                    <h3 x-text="branchForm.mode === 'create' ? 'Add Branch' : 'Edit Branch'">Edit Branch</h3>
                    <button type="button" class="modal-close" @click="closeAll()">&times;</button>
                </div>
                <form :action="branchForm.action" method="POST">
                    @csrf
                    <input type="hidden" name="_redirect" value="master-data">
                    <template x-if="branchForm.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-body">
                        <div class="grid cols-2">
                            <div class="field mb-4"><label for="branch_code">Branch Code</label><input class="input" id="branch_code" type="number" name="branch_code" x-model.number="branchForm.branchCode" required min="1"></div>
                            <div class="field mb-4"><label for="ext_branch_code">Ext Branch Code</label><input class="input" id="ext_branch_code" type="text" name="ext_branch_code" x-model="branchForm.extBranchCode" required maxlength="10"></div>
                            <div class="field mb-4"><label for="branch_name">Branch Name</label><input class="input" id="branch_name" type="text" name="branch_name" x-model="branchForm.branchName" required></div>
                            <div class="field mb-4"><label for="branch_display_name">Display Name</label><input class="input" id="branch_display_name" type="text" name="display_name" x-model="branchForm.displayName"></div>
                            <div class="field mb-4">
                                <label for="branch_province_id">Province</label>
                                <select class="input" id="branch_province_id" name="province_id" x-model="branchForm.provinceId" @change="branchForm.districtId = ''" required>
                                    <option value="">Select Province</option>
                                    @foreach ($allProvinces as $province)
                                        <option value="{{ $province->province_id }}">{{ $province->province_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field mb-4">
                                <label for="branch_district_id">District</label>
                                <select class="input" id="branch_district_id" name="district_id" x-model="branchForm.districtId" :disabled="!branchForm.provinceId" required>
                                    <option value="" x-text="branchForm.provinceId ? 'Select District' : 'Select Province First'"></option>
                                    @foreach ($allDistricts as $district)
                                        <option value="{{ $district->district_id }}" data-province-id="{{ $district->province_id }}" x-show="branchForm.provinceId === '{{ $district->province_id }}'">{{ $district->district_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field mb-4">
                                <label for="branch_fiscal_year">Fiscal Year</label>
                                <select class="input" id="branch_fiscal_year" name="fiscal_year" x-model="branchForm.fiscalYear" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach ($fiscalYears as $fiscalYear)
                                        <option value="{{ $fiscalYear }}">{{ $fiscalYear }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field mb-4">
                                <label for="branch_month">Month</label>
                                <select class="input" id="branch_month" name="month" x-model="branchForm.month" required>
                                    <option value="">Select Month</option>
                                    @foreach ($monthNames as $monthValue => $monthName)
                                        <option value="{{ $monthValue }}">{{ $monthName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field mb-4"><label for="local_level">Local Level</label><input class="input" id="local_level" type="number" name="local_level" x-model.number="branchForm.localLevel" min="1"></div>
                            <div class="field mb-4"><label for="branch_address">Address</label><input class="input" id="branch_address" type="text" name="address" x-model="branchForm.address"></div>
                            <div class="field mb-4">
                                <label for="branch_status">Status</label>
                                <select class="input" id="branch_status" name="status" x-model="branchForm.status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <template x-if="branchForm.status === 'inactive'">
                                <div class="field mb-4">
                                    <label for="branch_inactive_fiscal_year">Inactive Fiscal Year</label>
                                    <select class="input" id="branch_inactive_fiscal_year" name="inactive_fiscal_year" x-model="branchForm.inactiveFiscalYear" :required="branchForm.status === 'inactive'">
                                        <option value="">Select Fiscal Year</option>
                                        @foreach ($fiscalYears as $fiscalYear)
                                            <option value="{{ $fiscalYear }}">{{ $fiscalYear }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </template>
                            <template x-if="branchForm.status === 'inactive'">
                                <div class="field mb-4">
                                    <label for="branch_inactive_month">Inactive Month</label>
                                    <select class="input" id="branch_inactive_month" name="inactive_month" x-model="branchForm.inactiveMonth" :required="branchForm.status === 'inactive'">
                                        <option value="">Select Month</option>
                                        @foreach ($monthNames as $monthValue => $monthName)
                                            <option value="{{ $monthValue }}">{{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-outline" @click="closeAll()">Cancel</button>
                        <button type="submit" class="btn btn-primary" x-text="branchForm.mode === 'create' ? 'Save Branch' : 'Update Branch'">Update Branch</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============== AGENT / SURVEYOR PERIOD MODAL ============== --}}
        <div class="modal-overlay" x-show="modals.personnel" x-cloak x-transition.opacity @keydown.escape.window="closeAll()" @click.self="closeAll()">
            <div class="modal-box" x-transition.scale.origin.center>
                <div class="modal-head">
                    <h3 x-text="personnelForm.mode === 'create' ? 'Add Period Update' : 'Edit Period Update'">Period Update</h3>
                    <button type="button" class="modal-close" @click="closeAll()">&times;</button>
                </div>
                <form :action="personnelForm.action" method="POST">
                    @csrf
                    <template x-if="personnelForm.mode === 'edit'"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="modal-body">
                        <div class="grid cols-2">
                            <div class="field mb-4">
                                <label for="personnel_type">Data Type</label>
                                <select class="input" id="personnel_type" name="type" x-model="personnelForm.type" required>
                                    <option value="agent">Licensed Agents</option>
                                    <option value="surveyor">Surveyors</option>
                                </select>
                            </div>
                            <div class="field mb-4">
                                <label for="personnel_number">Total Number</label>
                                <input class="input" id="personnel_number" type="number" name="number" x-model.number="personnelForm.number" required min="0">
                            </div>
                            <div class="field mb-4">
                                <label for="personnel_fiscal_year">Fiscal Year</label>
                                <select class="input" id="personnel_fiscal_year" name="fiscal_year" x-model="personnelForm.fiscalYear" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach ($fiscalYears as $fiscalYear)
                                        <option value="{{ $fiscalYear }}">{{ $fiscalYear }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field mb-4">
                                <label for="personnel_month">Month</label>
                                <select class="input" id="personnel_month" name="month" x-model="personnelForm.month" required>
                                    <option value="">Select Month</option>
                                    @foreach ($monthNames as $monthValue => $monthName)
                                        <option value="{{ $monthValue }}">{{ $monthName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="text-muted" style="margin:0; font-size:12px;">Add a new total whenever the number changes. Historical period updates are retained for dashboard filters.</p>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-outline" @click="closeAll()">Cancel</button>
                        <button type="submit" class="btn btn-primary" x-text="personnelForm.mode === 'create' ? 'Save Update' : 'Update Record'">Save Update</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function masterData() {
            return {
                modals: { province: false, district: false, policy: false, branch: false, personnel: false },
                provinceForm: { mode: 'create', action: '', name: '', code: '' },
                districtForm: { mode: 'create', action: '', provinceId: '', name: '', code: '' },
                policyForm: { mode: 'create', action: '', policyId: null, parentId: '', name: '', nameNp: '', code: '', status: 'active' },
                branchForm: { mode: 'create', action: '', branchCode: null, extBranchCode: '', branchName: '', provinceId: '', districtId: '', fiscalYear: '', month: '', localLevel: null, address: '', displayName: '', status: 'active', inactiveFiscalYear: '', inactiveMonth: '' },
                personnelForm: { mode: 'create', action: '', type: 'agent', fiscalYear: '', month: '', number: 0 },

                addProvince() {
                    this.closeAll();
                    this.provinceForm.mode = 'create';
                    this.provinceForm.action = '{{ route('provinces.store') }}';
                    this.provinceForm.name = '';
                    this.provinceForm.code = '';
                    this.modals.province = true;
                },

                addDistrict() {
                    this.closeAll();
                    this.districtForm.mode = 'create';
                    this.districtForm.action = '{{ route('districts.store') }}';
                    this.districtForm.provinceId = '';
                    this.districtForm.name = '';
                    this.districtForm.code = '';
                    this.modals.district = true;
                },


                addBranch() {
                    this.closeAll();
                    this.branchForm = { mode: 'create', action: '{{ route('branches.store') }}', branchCode: null, extBranchCode: '', branchName: '', provinceId: '', districtId: '', fiscalYear: '', month: '', localLevel: null, address: '', displayName: '', status: 'active', inactiveFiscalYear: '', inactiveMonth: '' };
                    this.modals.branch = true;
                },

                addPolicy() {
                    this.closeAll();
                    this.policyForm.mode = 'create';
                    this.policyForm.action = '{{ route('policies.store') }}';
                    this.policyForm.policyId = null;
                    this.policyForm.parentId = '';
                    this.policyForm.name = '';
                    this.policyForm.nameNp = '';
                    this.policyForm.code = '';
                    this.policyForm.status = 'active';
                    this.modals.policy = true;
                },

                addPersonnel() {
                    this.closeAll();
                    this.personnelForm = { mode: 'create', action: '{{ route('network-personnel.store') }}', type: 'agent', fiscalYear: '', month: '', number: 0 };
                    this.modals.personnel = true;
                },

                openProvince(id, name, code) {
                    this.closeAll();
                    this.provinceForm.mode = 'edit';
                    this.provinceForm.action = '{{ route('provinces.update', '__ID__') }}'.replace('__ID__', id);
                    this.provinceForm.name = name;
                    this.provinceForm.code = code || '';
                    this.modals.province = true;
                },

                openDistrict(id, provinceId, name, code) {
                    this.closeAll();
                    this.districtForm.mode = 'edit';
                    this.districtForm.action = '{{ route('districts.update', '__ID__') }}'.replace('__ID__', id);
                    this.districtForm.provinceId = String(provinceId);
                    this.districtForm.name = name;
                    this.districtForm.code = code || '';
                    this.modals.district = true;
                },

                openPolicy(id, parentId, name, nameNp, status, code) {
                    this.closeAll();
                    this.policyForm.mode = 'edit';
                    this.policyForm.action = '{{ route('policies.update', '__ID__') }}'.replace('__ID__', id);
                    this.policyForm.policyId = id;
                    this.policyForm.parentId = parentId ? String(parentId) : '';
                    this.policyForm.name = name;
                    this.policyForm.nameNp = nameNp;
                    this.policyForm.code = code || '';
                    this.policyForm.status = status;
                    this.modals.policy = true;
                },


                openBranch(id, branchCode, extBranchCode, branchName, provinceId, districtId, fiscalYear, month, localLevel, address, displayName, status, inactiveFiscalYear, inactiveMonth) {
                    this.closeAll();
                    this.branchForm = { mode: 'edit', action: '{{ route('branches.update', '__ID__') }}'.replace('__ID__', id), branchCode, extBranchCode, branchName, provinceId: String(provinceId), districtId: String(districtId), fiscalYear, month: month ? String(month) : '', localLevel: localLevel || null, address, displayName, status, inactiveFiscalYear, inactiveMonth: inactiveMonth ? String(inactiveMonth) : '' };
                    this.modals.branch = true;
                },

                openPersonnel(id, type, fiscalYear, month, number) {
                    this.closeAll();
                    this.personnelForm = { mode: 'edit', action: '{{ route('network-personnel.update', '__ID__') }}'.replace('__ID__', id), type, fiscalYear, month: String(month), number };
                    this.modals.personnel = true;
                },

                closeAll() {
                    this.modals.province = false;
                    this.modals.district = false;
                    this.modals.policy = false;
                    this.modals.branch = false;
                    this.modals.personnel = false;
                },
            };
        }
    </script>
</x-app-layout>
