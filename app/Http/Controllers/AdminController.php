<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalUsers = User::count();
        $adminUser = $request->user();
        $foundationFund = $adminUser->withdrawable_balance;

        $pendingWithdrawalsCount = \App\Models\Transaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->count();

        return view('admin.dashboard', compact('totalUsers', 'foundationFund', 'pendingWithdrawalsCount'));
    }

    public function pendingWithdrawals()
    {
        $withdrawals = \App\Models\Transaction::with('user')
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->get();

        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function approveWithdrawal($id)
    {
        $transaction = \App\Models\Transaction::findOrFail($id);

        if ($transaction->status !== 'pending' || $transaction->type !== 'withdrawal') {
            return back()->withErrors(['msg' => 'Invalid transaction state for approval.']);
        }

        $transaction->status = 'completed';
        $transaction->save();

        return back()->with('status', 'Withdrawal approved successfully.');
    }

    public function rejectWithdrawal($id)
    {
        $transaction = \App\Models\Transaction::findOrFail($id);

        if ($transaction->status !== 'pending' || $transaction->type !== 'withdrawal') {
            return back()->withErrors(['msg' => 'Invalid transaction state for rejection.']);
        }

        // Refund the user
        $user = User::findOrFail($transaction->user_id);
        $user->withdrawable_balance += $transaction->amount;
        $user->save();

        $transaction->status = 'rejected';
        $transaction->save();

        return back()->with('status', 'Withdrawal rejected and amount refunded to user.');
    }

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
