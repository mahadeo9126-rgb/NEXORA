<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\BscScanService;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Exception;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function upgrade(Request $request, BscScanService $bsc, CommissionService $commission)
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:7',
            'tx_hash' => 'required|string|unique:transactions'
        ]);

        $user = $request->user();

        if ($user->rub_rank !== ($request->level - 1)) {
            return back()->withErrors(['level' => 'You must upgrade sequentially.']);
        }

        try {
            $prices = [1 => 60, 2 => 115, 3 => 170, 4 => 225, 5 => 280, 6 => 550, 7 => 1200];
            $expectedAmount = (float) $prices[$request->level];

            $bsc->verifyDeposit($request->tx_hash, $user->wallet_address, $expectedAmount);

            Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $request->tx_hash,
                'type' => 'upgrade',
                'amount' => $prices[$request->level],
                'status' => 'completed',
            ]);

            $commission->processUpgrade($user, $request->level);

            return back()->with('status', "Successfully upgraded to RUB {$request->level}");

        } catch (Exception $e) {
            return back()->withErrors(['tx_hash' => $e->getMessage()]);
        }
    }

    public function subscribe(Request $request, BscScanService $bsc, CommissionService $commission)
    {
        $request->validate([
            'tx_hash' => 'required|string|unique:transactions'
        ]);

        $user = $request->user();

        try {
            $bsc->verifyDeposit($request->tx_hash, $user->wallet_address, 39.0);

            Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $request->tx_hash,
                'type' => 'subscription',
                'amount' => 39,
                'status' => 'completed',
            ]);

            $commission->processSubscription($user);

            return back()->with('status', "Subscription renewed successfully.");

        } catch (Exception $e) {
            return back()->withErrors(['tx_hash' => $e->getMessage()]);
        }
    }

    public function requestWithdrawal(Request $request)
    {
        $user = $request->user();

        $isRestricted = $user->last_subscription_at ? Carbon::now()->diffInDays($user->last_subscription_at) > 30 : true;
        if ($isRestricted) {
            return back()->withErrors(['withdraw' => 'You must renew your $39 subscription to enable withdrawals.']);
        }

        if ($user->withdrawal_locked_until && Carbon::now()->isBefore($user->withdrawal_locked_until)) {
            return back()->withErrors(['withdraw' => 'Withdrawals are currently locked due to a recent wallet change.']);
        }

        if ($user->withdrawable_balance < 10) { // Minimum example
            return back()->withErrors(['withdraw' => 'Insufficient balance.']);
        }

        // Generate and send OTP via Mail (mocked for prompt)
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->save();

        // Mail::raw("Your withdrawal OTP is: {$otp}", function($msg) use ($user) {
        //     $msg->to($user->email)->subject('Withdrawal OTP');
        // });

        return back()->with('status', 'OTP sent to your email. Please verify to process withdrawal.');
    }

    public function verifyWithdrawal(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'amount' => 'required|numeric|min:10'
        ]);

        $user = $request->user();

        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        if ($user->withdrawable_balance < $request->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        // Process withdrawal
        $user->withdrawable_balance -= $request->amount;
        $user->otp_code = null;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'tx_hash' => 'WD_' . uniqid(), // Internal hash since it's an outbound Tx
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'status' => 'pending', // Admin to process
        ]);

        return back()->with('status', 'Withdrawal request submitted successfully.');
    }

    public function updateWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|unique:users'
        ]);

        $user = $request->user();
        $user->wallet_address = $request->wallet_address;
        $user->withdrawal_locked_until = Carbon::now()->addHours(24);
        $user->save();

        return back()->with('status', 'Wallet updated. Withdrawals locked for 24 hours for security.');
    }
}
