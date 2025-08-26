<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApprovalRequest;
use App\Http\Requests\Api\RejectRequest;
use App\Models\Request as BaseRequest;
use App\Models\RequestApproval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Get pending requests for the authenticated user to approve.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending(Request $request)
    {
        try {
            $pendingRequests = $this->approvalService->getPendingRequestsForApprover(Auth::user());

            return response()->json([
                'status' => 'success',
                'data' => $pendingRequests,
                'message' => 'Pending requests retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve pending requests',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Approve a request.
     *
     * @param  \App\Http\Requests\Api\ApprovalRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(ApprovalRequest $request)
    {
        try {
            $validated = $request->validated();

            $baseRequest = BaseRequest::where('id', $validated['request_id'])
                ->where('request_type', $validated['request_type'])
                ->first();

            if (!$baseRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request not found'
                ], 404);
            }

            // Process the approval
            $approval = $this->approvalService->processApproval(
                $baseRequest,
                Auth::user(),
                'approved',
                $validated['comments'] ?? null
            );

            // Load relationships
            $baseRequest->load(['user', 'approvals.approver']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'request' => $baseRequest,
                    'approval' => $approval
                ],
                'message' => 'Request approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Reject a request.
     *
     * @param  \App\Http\Requests\Api\RejectRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(RejectRequest $request)
    {
        try {
            $validated = $request->validated();

            $baseRequest = BaseRequest::where('id', $validated['request_id'])
                ->where('request_type', $validated['request_type'])
                ->first();

            if (!$baseRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request not found'
                ], 404);
            }

            // Process the rejection
            $approval = $this->approvalService->processApproval(
                $baseRequest,
                Auth::user(),
                'rejected',
                $validated['comments']
            );

            // Load relationships
            $baseRequest->load(['user', 'approvals.approver']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'request' => $baseRequest,
                    'approval' => $approval
                ],
                'message' => 'Request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reject request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Display the specified request.
     *
     * @param  string  $type
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($type, $id)
    {
        try {
            if (!in_array($type, ['leave', 'mission'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request type'
                ], 400);
            }

            $request = BaseRequest::where('request_type', $type)
                ->where('id', $id)
                ->with(['user', 'approvals.approver'])
                ->first();

            if (!$request) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request not found'
                ], 404);
            }

            // Add specific request details based on type
            if ($type === 'leave') {
                $request->load('leaveRequest');
            } elseif ($type === 'mission') {
                $request->load('missionRequest');
            }

            return response()->json([
                'status' => 'success',
                'data' => $request,
                'message' => 'Request retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
