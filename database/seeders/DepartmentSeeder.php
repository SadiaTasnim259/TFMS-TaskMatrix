<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department of Computer Science',
                'active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Department of Information Technology',
                'active' => true,
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'SE',
                'description' => 'Department of Software Engineering',
                'active' => true,
            ],
            [
                'name' => 'Cyber Security',
                'code' => 'CYS',
                'description' => 'Department of Cyber Security',
                'active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
