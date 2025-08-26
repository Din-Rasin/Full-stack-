<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'department_admin', 'description' => 'Department Administrator'],
            ['name' => 'team_leader', 'description' => 'Team Leader'],
            ['name' => 'hr_manager', 'description' => 'HR Manager'],
            ['name' => 'cfo', 'description' => 'Chief Financial Officer'],
            ['name' => 'ceo', 'description' => 'Chief Executive Officer'],
            ['name' => 'employee', 'description' => 'Employee'],
        ];

        foreach ($roles as $roleData) {
            \App\Models\Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );
        }

        // Create departments
        $departments = [
            ['name' => 'IT Department', 'code' => 'IT', 'description' => 'Information Technology Department'],
            ['name' => 'Sales Department', 'code' => 'SALES', 'description' => 'Sales Department'],
        ];

        foreach ($departments as $deptData) {
            \App\Models\Department::firstOrCreate(
                ['code' => $deptData['code']],
                [
                    'name' => $deptData['name'],
                    'description' => $deptData['description'],
                    'is_active' => true
                ]
            );
        }

        // Create admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign admin role to admin user
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole->id);
        }

        // Create IT department head
        $itHead = \App\Models\User::firstOrCreate(
            ['email' => 'ithead@example.com'],
            [
                'name' => 'IT Department Head',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign department_admin role to IT head
        $deptAdminRole = \App\Models\Role::where('name', 'department_admin')->first();
        if ($deptAdminRole && !$itHead->hasRole('department_admin')) {
            $itHead->roles()->attach($deptAdminRole->id);
        }

        // Assign IT department to IT head
        $itDept = \App\Models\Department::where('code', 'IT')->first();
        if ($itDept) {
            $itHead->departments()->syncWithoutDetaching([$itDept->id]);
        }

        // Create Sales department head
        $salesHead = \App\Models\User::firstOrCreate(
            ['email' => 'saleshead@example.com'],
            [
                'name' => 'Sales Department Head',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign department_admin role to Sales head
        if ($deptAdminRole && !$salesHead->hasRole('department_admin')) {
            $salesHead->roles()->attach($deptAdminRole->id);
        }

        // Assign Sales department to Sales head
        $salesDept = \App\Models\Department::where('code', 'SALES')->first();
        if ($salesDept) {
            $salesHead->departments()->syncWithoutDetaching([$salesDept->id]);
        }

        // Create team leaders
        $itTeamLeader = \App\Models\User::firstOrCreate(
            ['email' => 'itteamleader@example.com'],
            [
                'name' => 'IT Team Leader',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        $salesTeamLeader = \App\Models\User::firstOrCreate(
            ['email' => 'salesteamleader@example.com'],
            [
                'name' => 'Sales Team Leader',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign team_leader role to team leaders
        $teamLeaderRole = \App\Models\Role::where('name', 'team_leader')->first();
        if ($teamLeaderRole) {
            if (!$itTeamLeader->hasRole('team_leader')) {
                $itTeamLeader->roles()->attach($teamLeaderRole->id);
            }
            if (!$salesTeamLeader->hasRole('team_leader')) {
                $salesTeamLeader->roles()->attach($teamLeaderRole->id);
            }
        }

        // Assign departments to team leaders
        if ($itDept) {
            $itTeamLeader->departments()->syncWithoutDetaching([$itDept->id]);
        }
        if ($salesDept) {
            $salesTeamLeader->departments()->syncWithoutDetaching([$salesDept->id]);
        }

        // Create HR Manager
        $hrManager = \App\Models\User::firstOrCreate(
            ['email' => 'hrmanager@example.com'],
            [
                'name' => 'HR Manager',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign hr_manager role to HR Manager
        $hrManagerRole = \App\Models\Role::where('name', 'hr_manager')->first();
        if ($hrManagerRole && !$hrManager->hasRole('hr_manager')) {
            $hrManager->roles()->attach($hrManagerRole->id);
        }

        // Create CFO
        $cfo = \App\Models\User::firstOrCreate(
            ['email' => 'cfo@example.com'],
            [
                'name' => 'Chief Financial Officer',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign cfo role to CFO
        $cfoRole = \App\Models\Role::where('name', 'cfo')->first();
        if ($cfoRole && !$cfo->hasRole('cfo')) {
            $cfo->roles()->attach($cfoRole->id);
        }

        // Create CEO
        $ceo = \App\Models\User::firstOrCreate(
            ['email' => 'ceo@example.com'],
            [
                'name' => 'Chief Executive Officer',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign ceo role to CEO
        $ceoRole = \App\Models\Role::where('name', 'ceo')->first();
        if ($ceoRole && !$ceo->hasRole('ceo')) {
            $ceo->roles()->attach($ceoRole->id);
        }

        // Create regular employees
        $itEmployee = \App\Models\User::firstOrCreate(
            ['email' => 'itemployee@example.com'],
            [
                'name' => 'IT Employee',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        $salesEmployee = \App\Models\User::firstOrCreate(
            ['email' => 'salesemployee@example.com'],
            [
                'name' => 'Sales Employee',
                'password' => bcrypt('password'),
                'is_active' => true
            ]
        );

        // Assign employee role to employees
        $employeeRole = \App\Models\Role::where('name', 'employee')->first();
        if ($employeeRole) {
            if (!$itEmployee->hasRole('employee')) {
                $itEmployee->roles()->attach($employeeRole->id);
            }
            if (!$salesEmployee->hasRole('employee')) {
                $salesEmployee->roles()->attach($employeeRole->id);
            }
        }

        // Assign departments to employees
        if ($itDept) {
            $itEmployee->departments()->syncWithoutDetaching([$itDept->id]);
        }
        if ($salesDept) {
            $salesEmployee->departments()->syncWithoutDetaching([$salesDept->id]);
        }

        // Create workflows
        $itLeaveWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'IT Leave Workflow', 'type' => 'leave', 'department_id' => $itDept->id],
            [
                'approval_steps' => json_encode([
                    ['name' => 'Team Leader Approval', 'role' => 'team_leader'],
                    ['name' => 'HR Manager Approval', 'role' => 'hr_manager']
                ]),
                'is_active' => true,
                'created_by' => $itHead->id
            ]
        );

        $itMissionWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'IT Mission Workflow', 'type' => 'mission', 'department_id' => $itDept->id],
            [
                'approval_steps' => json_encode([
                    ['name' => 'Team Leader Approval', 'role' => 'team_leader'],
                    ['name' => 'CEO Approval', 'role' => 'ceo']
                ]),
                'is_active' => true,
                'created_by' => $itHead->id
            ]
        );

        $salesLeaveWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'Sales Leave Workflow', 'type' => 'leave', 'department_id' => $salesDept->id],
            [
                'approval_steps' => json_encode([
                    ['name' => 'Team Leader Approval', 'role' => 'team_leader'],
                    ['name' => 'CFO Approval', 'role' => 'cfo'],
                    ['name' => 'HR Manager Approval', 'role' => 'hr_manager']
                ]),
                'is_active' => true,
                'created_by' => $salesHead->id
            ]
        );

        $salesMissionWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'Sales Mission Workflow', 'type' => 'mission', 'department_id' => $salesDept->id],
            [
                'approval_steps' => json_encode([
                    ['name' => 'Team Leader Approval', 'role' => 'team_leader'],
                    ['name' => 'CFO Approval', 'role' => 'cfo'],
                    ['name' => 'HR Manager Approval', 'role' => 'hr_manager'],
                    ['name' => 'CEO Approval', 'role' => 'ceo']
                ]),
                'is_active' => true,
                'created_by' => $salesHead->id
            ]
        );
    }
}
