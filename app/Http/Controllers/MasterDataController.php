<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\District;
use App\Models\Policy;
use App\Models\Province;
use App\Support\NepaliFiscalCalendar;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function __invoke(): View
    {
        $monthNames = NepaliFiscalCalendar::monthNames();
        $fiscalYears = NepaliFiscalCalendar::fiscalYearOptions();
        $provinces = Province::withCount(['districts', 'branches'])->orderBy('province_id')->get();
        $districts = District::with('province')->withCount('branches')->orderBy('district_id')->get();
        $policies = Policy::orderBy('policy_id')->get();
        $branches = Branch::with(['province', 'district'])->orderBy('branch_code')->get();
        $complainTypes = DB::table('complain_types')->orderBy('id')->get();
        $allProvinces = Province::orderBy('province_name')->get(['province_id', 'province_name']);
        $allDistricts = District::with('province')->orderBy('district_name')->get(['district_id', 'province_id', 'district_name']);
        $parentPolicies = Policy::whereNull('parent_id')->orderBy('policy_name')->get(['policy_id', 'policy_name']);

        return view('master-data.index', compact(
            'provinces',
            'districts',
            'policies',
            'branches',
            'complainTypes',
            'allProvinces',
            'allDistricts',
            'parentPolicies',
            'fiscalYears',
            'monthNames'
        ));
    }
}
