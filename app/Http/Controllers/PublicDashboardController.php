<?php

namespace App\Http\Controllers;

use App\Services\PublicDashboardData;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    public function __invoke(PublicDashboardData $dashboardData): View
    {
        return view('public-dashboard', $dashboardData->toArray());
    }
}
