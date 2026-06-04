<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Floor;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Models\Charge;
use Modules\Penalty\Models\PenaltyRule;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\User;
use Tests\TestCase;

class PenaltyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_non_financial_zero_point_penalty_without_charge(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'dorm-admin',
        ]);

        $student = User::query()->create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'student',
            'discipline_limit' => 10,
        ]);

        $building = Building::query()->create([
            'address' => 'Test address',
            'total_floors' => 1,
        ]);

        $floor = Floor::query()->create([
            'building_id' => $building->id,
            'floor_number' => 1,
        ]);

        $room = Room::query()->create([
            'floor_id' => $floor->id,
            'room_number' => 101,
            'capacity' => 2,
            'live_cap' => 1,
        ]);

        $settlement = Settlement::query()->create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'start_at' => now()->toDateString(),
            'end_at' => null,
            'status' => 'active',
            'source' => 'admin_manual',
        ]);

        $rule = PenaltyRule::query()->create([
            'code' => 'warning',
            'title' => 'Warning',
            'default_points' => 0,
            'redeemable' => true,
            'creates_financial_charge' => false,
            'financial_amount' => null,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/penalties', [
            'user_id' => $student->id,
            'rule_id' => $rule->id,
            'description' => 'Non-financial warning',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('penalties', [
            'user_id' => $student->id,
            'settlement_id' => $settlement->id,
            'rule_id' => $rule->id,
            'created_by' => $admin->id,
            'points' => 0,
            'status' => 'active',
        ]);
        $this->assertSame(0, Charge::query()->where('user_id', $student->id)->count());
    }

    public function test_reaching_discipline_limit_notifies_student_and_manager(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-limit@example.com',
            'password' => 'password',
            'role' => 'dorm-admin',
        ]);

        $manager = User::query()->create([
            'name' => 'Manager',
            'email' => 'manager-limit@example.com',
            'password' => 'password',
            'role' => 'manager',
        ]);

        $student = User::query()->create([
            'lastname' => 'Studentov',
            'name' => 'Limit',
            'email' => 'student-limit@example.com',
            'password' => 'password',
            'role' => 'student',
            'discipline_limit' => 10,
        ]);

        $building = Building::query()->create([
            'address' => 'Limit address',
            'total_floors' => 1,
        ]);

        $floor = Floor::query()->create([
            'building_id' => $building->id,
            'floor_number' => 1,
        ]);

        $room = Room::query()->create([
            'floor_id' => $floor->id,
            'room_number' => 202,
            'capacity' => 2,
            'live_cap' => 1,
        ]);

        $settlement = Settlement::query()->create([
            'user_id' => $student->id,
            'room_id' => $room->id,
            'start_at' => now()->toDateString(),
            'end_at' => null,
            'status' => 'active',
            'source' => 'admin_manual',
        ]);

        $rule = PenaltyRule::query()->create([
            'code' => 'limit',
            'title' => 'Limit penalty',
            'default_points' => 10,
            'redeemable' => true,
            'creates_financial_charge' => false,
            'financial_amount' => null,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/penalties', [
            'user_id' => $student->id,
            'rule_id' => $rule->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.discipline.active_points', 10)
            ->assertJsonPath('data.discipline.discipline_limit', 10)
            ->assertJsonPath('data.discipline.settlement_closed', true);

        $settlement->refresh();
        $this->assertSame('finished', $settlement->status);
        $this->assertSame('discipline', $settlement->end_reason);
        $this->assertNotNull($settlement->end_at);

        $studentNotification = $student->notifications()->first();
        $managerNotification = $manager->notifications()->first();

        $this->assertNotNull($studentNotification);
        $this->assertNotNull($managerNotification);
        $this->assertSame('discipline_limit_reached', $studentNotification->data['notification_type']);
        $this->assertSame('discipline_limit_reached', $managerNotification->data['notification_type']);
        $this->assertSame('/penalty', $studentNotification->data['action_url']);
        $this->assertSame('/manager/penalties', $managerNotification->data['action_url']);
        $this->assertSame(10, $studentNotification->data['active_points']);
        $this->assertSame(10, $managerNotification->data['discipline_limit']);
    }
}
