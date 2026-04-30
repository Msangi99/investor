<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LegacyBaselineSeeder::class,
            LegacyRoleMatrixSeeder::class,
            LegacyRoleUsersSeeder::class,
        ]);
    }
}
