<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class CommissionService
{
    private $upgradePrices = [
        1 => ['total' => 60, 'slice_amount' => 50, 'credit' => 10, 'ranks' => [1]],
        2 => ['total' => 115, 'slice_amount' => 50, 'credit' => 15, 'ranks' => [2, 3]],
        3 => ['total' => 170, 'slice_amount' => 50, 'credit' => 20, 'ranks' => [3, 4, 5]],
        4 => ['total' => 225, 'slice_amount' => 50, 'credit' => 25, 'ranks' => [4, 5, 6, 7]],
        5 => ['total' => 280, 'slice_amount' => 50, 'credit' => 30, 'ranks' => [5, 6, 7, 7, 7]],
        6 => ['total' => 550, 'slice_amount' => 50, 'credit' => 0, 'ranks' => [6, 7, 7, 7, 7, 7, 7, 7, 7, 7], 'rv_pool' => 25, 'matching_pool' => 25],
        7 => ['total' => 1200, 'slice_amount' => 100, 'credit' => 0, 'ranks' => [7, 7, 7, 7, 7, 7, 7, 7, 7, 7], 'rv_pool' => 100, 'matching_pool' => 100],
    ];

    public function processUpgrade(User $user, int $level)
    {
        if ($user->rub_rank !== ($level - 1)) {
            throw new \Exception("Upgrades must be sequential.");
        }

        if (!isset($this->upgradePrices[$level])) return;

        $priceInfo = $this->upgradePrices[$level];

        // Marketplace Credit
        if (isset($priceInfo['credit']) && $priceInfo['credit'] > 0) {
            $user->shopping_credit += $priceInfo['credit'];
        }
        $user->rub_rank = $level;
        $user->save();

        // Upline Differential Step-Up Payout
        $currentSearchNode = User::find($user->sponsor_id);
        $sliceAmount = $priceInfo['slice_amount'];
        $ranksSequence = $priceInfo['ranks'];

        $admin = User::where('is_admin', 1)->first();

        foreach ($ranksSequence as $requiredRank) {
            $paidSlice = false;

            while ($currentSearchNode) {
                $isRestricted = $currentSearchNode->last_subscription_at ? Carbon::now()->diffInDays($currentSearchNode->last_subscription_at) > 30 : true;

                if (!$isRestricted && $currentSearchNode->rub_rank >= $requiredRank) {
                    $currentSearchNode->withdrawable_balance += $sliceAmount;
                    $currentSearchNode->save();
                    $paidSlice = true;
                    // Move the search node UP for the NEXT slice
                    $currentSearchNode = User::find($currentSearchNode->sponsor_id);
                    break;
                }

                // Keep searching UP for this current slice
                $currentSearchNode = User::find($currentSearchNode->sponsor_id);
            }

            // If we reached the top and couldn't find a qualified leader for this slice, pay Admin
            if (!$paidSlice && $admin) {
                $admin->withdrawable_balance += $sliceAmount;
                $admin->save();
            }
        }

        // Special RUB 6 and 7 Logic
        if ($level === 6 || $level === 7) {
            // 1. RV Pool Distribution
            $rvTotal = $priceInfo['rv_pool'];
            $rvPerLevel = $rvTotal / 20;

            $parent = User::find($user->parent_id);
            $levelCount = 1;
            while ($parent && $levelCount <= 20) {
                $isRestricted = $parent->last_subscription_at ? Carbon::now()->diffInDays($parent->last_subscription_at) > 30 : true;
                if (!$isRestricted) {
                    $parent->withdrawable_balance += $rvPerLevel;
                    $parent->save();
                    $levelCount++;
                }
                $parent = User::find($parent->parent_id);
            }

            // 2. Matching Pool Distribution (3 Generations)
            $matchingTotal = $priceInfo['matching_pool'];
            $percentages = [1 => 0.50, 2 => 0.30, 3 => 0.20];

            $sponsor = User::find($user->sponsor_id);
            $genCount = 1;

            while ($sponsor && $genCount <= 3) {
                $bonus = $matchingTotal * $percentages[$genCount];
                $sponsor->withdrawable_balance += $bonus;
                $sponsor->save();

                $sponsor = User::find($sponsor->sponsor_id);
                $genCount++;
            }
        }
    }

    public function processSubscription(User $user)
    {
        $user->last_subscription_at = Carbon::now();
        $user->save();

        $rvAmount = 1.40;

        // 1. Matrix RV (20 Levels Up)
        $parent = User::find($user->parent_id);
        $levelCount = 1;
        while ($parent && $levelCount <= 20) {
            $isRestricted = $parent->last_subscription_at ? Carbon::now()->diffInDays($parent->last_subscription_at) > 30 : true;
            if (!$isRestricted) {
                $parent->withdrawable_balance += $rvAmount;
                $parent->save();

                // 2. Matching Bonus on the RV
                $this->processMatchingBonus($parent, $rvAmount);
                $levelCount++; // Increment only when active
            }
            // Dynamic compression
            $parent = User::find($parent->parent_id);
        }
    }

    private function processMatchingBonus(User $rvEarner, float $amount)
    {
        $percentages = [1 => 0.50, 2 => 0.30, 3 => 0.20]; // 50%, 30%, 20%

        $sponsor = User::find($rvEarner->sponsor_id);
        $genCount = 1;

        while ($sponsor && $genCount <= 3) {
            // Matching bonus pays EVEN IF restricted!
            $bonus = $amount * $percentages[$genCount];
            $sponsor->withdrawable_balance += $bonus;
            $sponsor->save();

            $sponsor = User::find($sponsor->sponsor_id);
            $genCount++;
        }
    }
}
