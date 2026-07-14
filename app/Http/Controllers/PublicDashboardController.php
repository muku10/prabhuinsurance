<?php

namespace App\Http\Controllers;

use App\Services\PublicDashboardData;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicDashboardController extends Controller
{
    public function __invoke(PublicDashboardData $dashboardData): View
    {
        $ttl = config('dashboard.public_cache_ttl');
        $data = $ttl > 0
            ? Cache::remember('public-dashboard:data:v4', $ttl, fn () => $dashboardData->toArray())
            : $dashboardData->toArray();

        return view('public-dashboard', $data);
    }
}
