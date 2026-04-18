<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Services\BscScanService;
use App\Services\PlacementService;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthController extends Controller
{
    public function showRegisterForm(Request $request)
    {
        $refId = $request->query('ref');
        return view('auth.register', compact('refId'));
    }

    public function register(Request $request, BscScanService $bsc, PlacementService $placement, CommissionService $commission)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'wallet_address' => 'required|string|unique:users',
            'tx_hash' => 'required|string|unique:transactions',
            'sponsor_id' => 'required|exists:users,id',
            'placement_pref' => 'required|in:extreme_left,left,right,extreme_right'
        ]);

        try {
            // Triple-Lock Security Gateway
            $bsc->verifyDeposit($request->tx_hash, $request->wallet_address);

            $sponsor = User::find($request->sponsor_id);

            // Dynamic Placement Engine
            $placementData = $placement->findPlacement($sponsor, $request->placement_pref);

            $user = new User([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'wallet_address' => $request->wallet_address,
                'sponsor_id' => $sponsor->id,
                'parent_id' => $placementData['parent_id'],
                'position' => $placementData['position'],
                'placement_pref' => $request->placement_pref,
            ]);
            $user->rub_rank = 1;
            $user->last_subscription_at = now();
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'tx_hash' => $request->tx_hash,
                'type' => 'registration',
                'amount' => 60, // RUB 1
                'status' => 'completed',
            ]);

            // Process Initial Upgrade & Subscription automatically if included in $60?
            // Actually, instruction says Registration requires Deposit.
            // Payouts for RUB 1:
            $commission->processUpgrade($user, 1);

            Auth::login($user);

            return redirect('/dashboard');

        } catch (Exception $e) {
            return back()->withErrors(['tx_hash' => $e->getMessage()]);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
