<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer')
            ->latest()
            ->paginate(20);

        return view('tenant.activity', compact('activities'));
    }
}
