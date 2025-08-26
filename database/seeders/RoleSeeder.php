<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Employee', 'description' => 'Regular employee'],
            ['name' => 'Team Leader', 'description' => 'Team leader role'],
            ['name' => 'HR Manager', 'description' => 'Human resources manager'],
            ['name' => 'CFO', 'description' => 'Chief Financial Officer'],
            ['name' => 'CEO', 'description' => 'Chief Executive Officer'],
            ['name' => 'Department Administrator', 'description' => 'Department administrator'],
            ['name' => 'System Administrator', 'description' => 'System administrator'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
