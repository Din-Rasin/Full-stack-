<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'IT Department',
                'description' => 'Information Technology Department',
                'code' => 'IT',
                'workflow_config' => json_encode([
                    'leave' => ['Team Leader', 'HR Manager'],
                    'mission' => ['Team Leader', 'CEO']
                ])
            ],
            [
                'name' => 'Sales Department',
                'description' => 'Sales and Marketing Department',
                'code' => 'SALES',
                'workflow_config' => json_encode([
                    'leave' => ['Team Leader', 'CFO', 'HR Manager'],
                    'mission' => ['Team Leader', 'CFO', 'HR Manager', 'CEO']
                ])
            ]
        ];

        foreach ($departments as $department) {
            \App\Models\Department::firstOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'description' => $department['description'],
                    'workflow_config' => $department['workflow_config']
                ]
            );
        }
    }
}
