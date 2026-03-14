<?php

namespace Modules\Gym\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gym\Models\GymPlan;

class GymDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GymPlan::query()->updateOrCreate(
            ['name' => 'Месячный абонемент'],
            [
                'total_sessions' => 12,
                'price' => 10000,
                'duration_days' => 30,
                'is_active' => true,
            ]
        );
    }
}
