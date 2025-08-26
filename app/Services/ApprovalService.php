<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Request;
use App\Models\RequestApproval;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
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
            $nextApprover = app(WorkflowService::class)->getNextApprover($request, $currentStep + 1);

            if ($nextApprover) {
                // There are more approvers, keep request as pending
                $request->update(['status' => 'pending']);
                // Notify next approver
                $this->notifyUser($nextApprover, 'New request pending your approval', 'A request is pending your approval.');
            } else {
                // No more approvers, request is fully approved
                $request->update(['status' => 'approved']);
                // Notify requester
                $this->notifyUser($request->user, 'Request approved', 'Your request has been fully approved.');
            }
        } else {
            // Request is rejected
            $request->update(['status' => 'rejected']);
            // Notify requester
            $this->notifyUser($request->user, 'Request rejected', 'Your request has been rejected.');
        }

        return $approval;
    }

    /**
     * Notify a user
     */
    public function notifyUser(User $user, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
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
}
