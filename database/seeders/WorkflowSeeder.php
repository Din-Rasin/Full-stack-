<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get departments
        $itDepartment = \App\Models\Department::where('code', 'IT')->first();
        $salesDepartment = \App\Models\Department::where('code', 'SALES')->first();

        // Get users for created_by field
        $adminUser = \App\Models\User::first();

        // Create workflows for IT department
        if ($itDepartment) {
            \App\Models\Workflow::create([
                'name' => 'IT Leave Workflow',
                'type' => 'leave',
                'department_id' => $itDepartment->id,
                'approval_steps' => json_encode([
                    ['role' => 'Team Leader', 'order' => 1],
                    ['role' => 'HR Manager', 'order' => 2]
                ]),
                'conditions' => json_encode([]),
                'is_active' => true,
                'created_by' => $adminUser->id
            ]);

            \App\Models\Workflow::create([
                'name' => 'IT Mission Workflow',
                'type' => 'mission',
                'department_id' => $itDepartment->id,
                'approval_steps' => json_encode([
                    ['role' => 'Team Leader', 'order' => 1],
                    ['role' => 'CEO', 'order' => 2]
                ]),
                'conditions' => json_encode([]),
                'is_active' => true,
                'created_by' => $adminUser->id
            ]);
        }

        // Create workflows for Sales department
        if ($salesDepartment) {
            \App\Models\Workflow::create([
                'name' => 'Sales Leave Workflow',
                'type' => 'leave',
                'department_id' => $salesDepartment->id,
                'approval_steps' => json_encode([
                    ['role' => 'Team Leader', 'order' => 1],
                    ['role' => 'CFO', 'order' => 2],
                    ['role' => 'HR Manager', 'order' => 3]
                ]),
                'conditions' => json_encode([]),
                'is_active' => true,
                'created_by' => $adminUser->id
            ]);

            \App\Models\Workflow::create([
                'name' => 'Sales Mission Workflow',
                'type' => 'mission',
                'department_id' => $salesDepartment->id,
                'approval_steps' => json_encode([
                    ['role' => 'Team Leader', 'order' => 1],
                    ['role' => 'CFO', 'order' => 2],
                    ['role' => 'HR Manager', 'order' => 3],
                    ['role' => 'CEO', 'order' => 4]
                ]),
                'conditions' => json_encode([]),
                'is_active' => true,
                'created_by' => $adminUser->id
            ]);
        }
    }
}
