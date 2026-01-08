<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the roles table with default roles
     * 
     * This creates 6 roles that will be used throughout the system
     */
    public function run(): void
    {
        // Define all roles
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'System administrator with full access',
            ],
            [
                'name' => 'HOD',
                'slug' => 'hod',
                'description' => 'Head of Department - manages staff and task forces',
            ],
            [
                'name' => 'PSM',
                'slug' => 'psm',
                'description' => 'Personnel & Staff Management - system operator',
            ],
            [
                'name' => 'Lecturer',
                'slug' => 'lecturer',
                'description' => 'Academic lecturer - assigned to task forces',
            ],
            [
                'name' => 'Management',
                'slug' => 'management',
                'description' => 'Faculty management - view reports and analytics',
            ],
            [
                'name' => 'TaskForceOwner',
                'slug' => 'task-force-owner',
                'description' => 'Owner of a task force - leads task force activities',
            ],
        ];

        // Insert each role into database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
