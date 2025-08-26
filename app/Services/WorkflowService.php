<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Request;
use App\Models\RequestApproval;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Support\Facades\Auth;

class WorkflowService
{
    /**
     * Get the workflow for a specific request type and department
     */
    public function getWorkflowForRequest($requestType, $departmentId)
    {
        return Workflow::where('type', $requestType)
            ->where('department_id', $departmentId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the next approver for a request
     */
    public function getNextApprover(Request $request, $currentStep = 0)
    {
        $workflow = $request->workflow;

        if (!$workflow) {
            return null;
        }

        $approvalSteps = json_decode($workflow->approval_steps, true);

        if (!$approvalSteps || !isset($approvalSteps[$currentStep])) {
            return null;
        }

        $step = $approvalSteps[$currentStep];
        $role = $step['role'];

        // Get users with the required role in the same department
        $approvers = User::whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->whereHas('departments', function ($query) use ($request) {
            $query->where('id', $request->user->departments->first()->id);
        })->get();

        return $approvers->first();
    }

    /**
     * Process a request approval
     */
    public function processApproval(Request $request, User $approver, $status, $comments = null)
    {
        $currentStep = $request->approvals()->count();

        // Create approval record
        $approval = RequestApproval::create([
            'request_id' => $request->id,
            'approver_id' => $approver->id,
            'step_number' => $currentStep + 1,
            'status' => $status,
            'comments' => $comments,
        ]);

        // Update request status
        if ($status === 'approved') {
            $nextApprover = $this->getNextApprover($request, $currentStep + 1);

            if ($nextApprover) {
                // There are more approvers, keep request as pending
                $request->update(['status' => 'pending']);
            } else {
                // No more approvers, request is fully approved
                $request->update(['status' => 'approved']);
            }
        } else {
            // Request is rejected
            $request->update(['status' => 'rejected']);
        }

        return $approval;
    }

    /**
     * Get pending requests for a user to approve
     */
    public function getPendingRequestsForApprover(User $user)
    {
        // Get requests where user is the next approver
        return Request::where('status', 'pending')
            ->whereHas('workflow', function ($query) use ($user) {
                $query->whereHas('department', function ($query) use ($user) {
                    $query->whereHas('users', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    });
                });
            })
            ->whereDoesntHave('approvals', function ($query) use ($user) {
                $query->where('approver_id', $user->id);
            })
            ->get();
    }

    /**
     * Create a new request with workflow
     */
    public function createRequest($requestData, User $user)
    {
        // Get user's department
        $department = $user->departments->first();

        if (!$department) {
            throw new \Exception('User must belong to a department');
        }

        // Get workflow for request type
        $workflow = $this->getWorkflowForRequest($requestData['request_type'], $department->id);

        if (!$workflow) {
            throw new \Exception('No workflow found for this request type and department');
        }

        // Create the request
        $request = Request::create([
            'request_type' => $requestData['request_type'],
            'user_id' => $user->id,
            'workflow_id' => $workflow->id,
            'request_data' => $requestData['request_data'] ?? [],
            'status' => 'pending',
            'start_date' => $requestData['start_date'] ?? null,
            'end_date' => $requestData['end_date'] ?? null,
            'reason' => $requestData['reason'] ?? null,
            'submitted_at' => now(),
        ]);

        return $request;
    }
}
