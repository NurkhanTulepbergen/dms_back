<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Models\User;
use Tests\TestCase;

class EmployeeStudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_only_students(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'password' => 'password',
            'role' => 'employee',
        ]);

        $student = User::query()->create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'student',
        ]);

        User::query()->create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => 'password',
            'role' => 'manager',
        ]);

        Sanctum::actingAs($employee);

        $response = $this->getJson('/api/v1/users');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $student->id)
            ->assertJsonPath('data.0.role', 'student');
    }

    public function test_employee_cannot_create_users(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee',
            'email' => 'employee-create@example.com',
            'password' => 'password',
            'role' => 'employee',
        ]);

        Sanctum::actingAs($employee);

        $this->postJson('/api/v1/users', [])
            ->assertForbidden();
    }
}
