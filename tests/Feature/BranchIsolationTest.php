<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_user_cannot_switch_to_unauthorized_branch(): void
    {
        $this->seed();

        $cbg2 = Branch::where('code', 'CBG002')->first();
        $kasirMenteng = User::where('email', 'kasir.cbg1@bakeryerp.local')->first();

        // Kasir Menteng (only assigned to CBG001) tries to switch to CBG002
        $response = $this->actingAs($kasirMenteng)->post('/branch/switch', [
            'branch_id' => $cbg2->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertNotEquals($cbg2->id, session('active_branch_id'));
    }

    public function test_owner_can_access_all_branches(): void
    {
        $this->seed();

        $cbg2 = Branch::where('code', 'CBG002')->first();
        $owner = User::where('email', 'owner@bakeryerp.local')->first();

        $response = $this->actingAs($owner)->post('/branch/switch', [
            'branch_id' => $cbg2->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals($cbg2->id, session('active_branch_id'));
    }
}
