<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CleanupUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all users except the one with email 'admin@tfms.local' or ID 1
        $count = User::where('email', '!=', 'admin@tfms.local')
            ->where('id', '!=', 1)
            ->delete();

        $this->command->info("Deleted {$count} users.");
    }
}
