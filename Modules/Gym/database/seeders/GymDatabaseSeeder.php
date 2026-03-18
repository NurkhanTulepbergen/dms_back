<?php

namespace Modules\Gym\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gym\Services\GymService;

class GymDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(GymService::class)->ensureDefaultPlansExist();
    }
}
