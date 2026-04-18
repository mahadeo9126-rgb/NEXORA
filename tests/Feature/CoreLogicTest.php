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
        $sponsor = User::factory()->create(['username' => 'sponsor', 'rub_rank' => 3, 'withdrawable_balance' => 0]);
        $user = User::factory()->create(['username' => 'user', 'sponsor_id' => $sponsor->id, 'rub_rank' => 2]);

        $service = new CommissionService();
        $service->processUpgrade($user, 3); // Upgrade to Level 3

        $sponsor->refresh();
        $this->assertEquals(150, $sponsor->withdrawable_balance); // Level 3 upline payout

        $user->refresh();
        $this->assertEquals(20, $user->shopping_credit); // Level 3 credit
        $this->assertEquals(3, $user->rub_rank);
    }
}
