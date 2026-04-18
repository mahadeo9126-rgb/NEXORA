<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $referralLink = url('/register?ref=' . $user->id);

        // Income Predictor: Example logic based on matrix size and rank
        $directs = $user->referrals()->count();
        $matrixSize = $user->children()->count(); // Simplified for 1st level, real implementation might count descendants
        $incomePredictor = ($matrixSize * 1.40) + ($directs * 5);

        // Foundation Fund (Mock global counter)
        $foundationFund = 15000.50;

        return view('dashboard', compact('user', 'referralLink', 'incomePredictor', 'foundationFund'));
    }
}
