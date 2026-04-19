<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $referralLink = url('/register?ref=' . $user->id);

        // Fetch recent transactions
        $recentTransactions = \App\Models\Transaction::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

        // Count descendants properly without N+1 problem
        $matrixSize = 0;
        $allDescendants = \App\Models\User::select('id', 'parent_id')->get()->groupBy('parent_id')->toArray();
        $queue = [$user->id];

        while(!empty($queue)) {
            $currentId = array_shift($queue);
            if (isset($allDescendants[$currentId])) {
                foreach ($allDescendants[$currentId] as $child) {
                    $matrixSize++;
                    array_push($queue, $child['id']);
                }
            }
        }

        // Foundation Fund (Mock global counter)
        $foundationFund = 15000.50;

        return view('dashboard', compact('user', 'referralLink', 'matrixSize', 'foundationFund', 'recentTransactions'));
    }
}
