<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'   => Tenant::count(),
            'active_tenants'  => Tenant::where('is_active', true)->count(),
            'trial_tenants'   => Tenant::whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->count(),
            'mrr'             => Tenant::whereHas('plan', fn($q) => $q->where('is_free', false))
                                    ->with('plan')->get()->sum(fn($t) => $t->plan->price_monthly ?? 0),
        ];

        $recentTenants = Tenant::with('plan')->latest()->limit(8)->get();

        $planDistribution = Plan::withCount('tenants')->get();

        return view('admin.dashboard', compact('stats', 'recentTenants', 'planDistribution'));
    }
}
