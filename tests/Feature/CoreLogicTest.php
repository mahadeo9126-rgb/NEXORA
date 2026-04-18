<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\PlacementService;
use App\Services\CommissionService;
use Carbon\Carbon;

class CoreLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_placement_extreme_left()
    {
        $sponsor = User::factory()->create(['username' => 'sponsor', 'rub_rank' => 1]);
        $service = new PlacementService();

        $res = $service->findPlacement($sponsor, 'extreme_left');
        $this->assertEquals($sponsor->id, $res['parent_id']);
        $this->assertEquals('left', $res['position']);
    }

    public function test_commission_upgrade_payout()
    {
        // Admin
        $admin = User::factory()->create(['username' => 'admin', 'is_admin' => 1, 'withdrawable_balance' => 0]);

        // Sponsor is rank 3
        $sponsor = User::factory()->create([
            'username' => 'sponsor',
            'rub_rank' => 3,
            'withdrawable_balance' => 0,
            'last_subscription_at' => now(), // Add subscription so not restricted
        ]);

        // User upgrading from 2 to 3
        $user = User::factory()->create(['username' => 'user', 'sponsor_id' => $sponsor->id, 'rub_rank' => 2]);

        $service = new CommissionService();
        $service->processUpgrade($user, 3); // Upgrade to Level 3. Slices needed: [3, 4, 5]

        $sponsor->refresh();
        // Sponsor catches the first slice (Rank 3 requirement)
        $this->assertEquals(50, $sponsor->withdrawable_balance);

        $admin->refresh();
        // Since there is no upline above sponsor, slices 4 and 5 roll up to Admin (50 + 50 = 100)
        $this->assertEquals(100, $admin->withdrawable_balance);

        $user->refresh();
        $this->assertEquals(20, $user->shopping_credit); // Level 3 credit
        $this->assertEquals(3, $user->rub_rank);
    }
}
