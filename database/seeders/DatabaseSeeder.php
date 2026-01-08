<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This method is called when running: php artisan db:seed
     */
    public function run(): void
    {
        // Run RoleSeeder first (roles must exist before users)
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            ConfigurationSeeder::class,
        ]);
    }
}
