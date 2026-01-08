<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with test users
     * 
     * Creates 6 test users with different roles for testing
     * 
     * Passwords:
     * - admin@tfms.local: Admin123
     * - hod_cs@tfms.local: Hod123
     * - hod_eng@tfms.local: Hod123
     * - psm@tfms.local: Psm123
     * - lecturer@tfms.local: Lecturer123
     * - mgmt@tfms.local: Mgmt123
     */
    public function run(): void
    {
        // Define all test users
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@tfms.local',
                'password' => 'Admin123',
                'role_name' => 'Admin',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Science HOD',
                'email' => 'hod_cs@tfms.local',
                'password' => 'Hod123',
                'role_name' => 'HOD',
                'is_active' => true,
            ],
            [
                'name' => 'Engineering HOD',
                'email' => 'hod_eng@tfms.local',
                'password' => 'Hod123',
                'role_name' => 'HOD',
                'is_active' => true,
            ],
            [
                'name' => 'PSM Officer',
                'email' => 'psm@tfms.local',
                'password' => 'Psm123',
                'role_name' => 'PSM',
                'is_active' => true,
            ],
            [
                'name' => 'Test Lecturer',
                'email' => 'lecturer@tfms.local',
                'password' => 'Lecturer123',
                'role_name' => 'Lecturer',
                'is_active' => true,
            ],
            [
                'name' => 'Management User',
                'email' => 'mgmt@tfms.local',
                'password' => 'Mgmt123',
                'role_name' => 'Management',
                'is_active' => true,
            ],
        ];

        // Create each user
        foreach ($users as $userData) {
            // Find the role by name
            $role = Role::where('name', $userData['role_name'])->first();

            // Create the user
            // Note: Password is hashed automatically via the 'hashed' cast
            // Assign Department and Staff ID based on role
            $department = null;
            $staffId = null;
            $isHod = false;

            if ($userData['role_name'] === 'HOD') {
                $isHod = true;
                if ($userData['email'] === 'hod_cs@tfms.local') {
                    $department = \App\Models\Department::where('code', 'CS')->first();
                } else {
                    $department = \App\Models\Department::where('code', 'SE')->first(); // Using SE as example
                }
            } elseif ($userData['role_name'] === 'Lecturer') {
                $department = \App\Models\Department::where('code', 'CS')->first();
            }

            if (in_array($userData['role_name'], ['HOD', 'Lecturer', 'PSM'])) {
                $staffId = 'S' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            }

            User::create([
                'name' => $userData['name'],
                'first_name' => explode(' ', $userData['name'])[0],
                'last_name' => explode(' ', $userData['name'])[1] ?? '',
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role_id' => $role?->id,
                'staff_id' => $staffId,
                'department_id' => $department?->id,
                'is_hod' => $isHod,
                'is_active' => $userData['is_active'],
                'is_first_login' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
