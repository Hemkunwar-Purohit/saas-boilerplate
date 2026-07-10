<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        $stats = [
            'total_users'     => User::count(),
            'active_users'    => User::where('is_active', true)->count(),
            'plan_name'       => $tenant->plan?->name ?? 'No plan',
            'trial_days_left' => $tenant->onTrial() ? now()->diffInDays($tenant->trial_ends_at) : null,
        ];

        // activity() helper nahi — seedha Activity model use karo
        $recentActivity = Activity::latest()->limit(5)->get();

        return view('tenant.dashboard', compact('stats', 'recentActivity'));
    }
}