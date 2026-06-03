<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role' => 'admin',
                'name' => 'Admin',
                'lastname' => 'System',
                'middlename' => null,
                'phone_number' => '+77770000000',
                'uni_id' => 'ADMIN001',
                'gender' => 'male',
                'discipline_limit' => 10,
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
    }
}
