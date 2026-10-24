<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SportCategorySeeder::class,
            BadmintonSkillLevelSeeder::class,
            GeographicDataSeeder::class,
            PlatformRoleSeeder::class,
            SystemSettingSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
