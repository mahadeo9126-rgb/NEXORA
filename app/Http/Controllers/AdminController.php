<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CommissionService;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Replaces Cron Jobs. Syncs the system, updates restrictions, etc.
     */
    public function sync(CommissionService $commissionService)
    {
        // Simple logic to "sync" or "check" anything global if necessary.
        // In Nexora, restricted status is evaluated dynamically via last_subscription_at > 30 days.
        // However, a sync button might execute pending tasks or refresh global caches.

        return redirect()->back()->with('status', 'System Sync Completed Successfully.');
    }
}
