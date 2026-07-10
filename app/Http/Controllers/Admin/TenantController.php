<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('plan')->latest()->paginate(20);
        return view('admin.tenants', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        return view('admin.tenant-show', compact('tenant'));
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        return back()->with('success', 'Tenant status updated.');
    }
}
