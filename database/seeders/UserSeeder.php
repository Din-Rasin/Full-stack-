<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
                'employee_id' => 'EMP001',
                'is_active' => true
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
                'phone' => '0987654321',
                'employee_id' => 'EMP002',
                'is_active' => true
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => bcrypt('password'),
                'phone' => '1112223333',
                'employee_id' => 'EMP003',
                'is_active' => true
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => bcrypt('password'),
                'phone' => '4445556666',
                'employee_id' => 'EMP004',
                'is_active' => true
            ]
        ];

        $roles = [
            'Employee' => [1, 2],
            'Team Leader' => [3],
            'HR Manager' => [4],
            'CFO' => [2],
            'CEO' => [1]
        ];

        $departments = [
            'IT' => [1, 3],
            'SALES' => [2, 4]
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::create($userData);

            // Assign roles
            foreach ($roles as $roleName => $userIds) {
                if (in_array($user->id, $userIds)) {
                    $role = \App\Models\Role::where('name', $roleName)->first();
                    if ($role) {
                        $user->roles()->attach($role->id);
                    }
                }
            }

            // Assign departments
            foreach ($departments as $deptCode => $userIds) {
                if (in_array($user->id, $userIds)) {
                    $department = \App\Models\Department::where('code', $deptCode)->first();
                    if ($department) {
                        $user->departments()->attach($department->id, ['is_primary' => true]);
                    }
                }
            }
        }
    }
}
