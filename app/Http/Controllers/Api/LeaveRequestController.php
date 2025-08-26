<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LeaveRequestStoreRequest;
use App\Http\Requests\Api\LeaveRequestUpdateRequest;
use App\Models\LeaveRequest;
use App\Models\Request as BaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = BaseRequest::where('request_type', 'leave');

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Apply date filters
            if ($request->has('start_date')) {
                $query->where('start_date', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->where('end_date', '<=', $request->end_date);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $leaveRequests = $query->with(['user', 'leaveRequest'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $leaveRequests,
                'message' => 'Leave requests retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leave requests',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\LeaveRequestStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LeaveRequestStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            // Create the base request
            $baseRequest = BaseRequest::create([
                'request_type' => 'leave',
                'user_id' => Auth::id(),
                'workflow_id' => 1, // This should be determined by the workflow service
                'request_data' => [
                    'leave_type' => $validated['leave_type'],
                    'days_requested' => $validated['days_requested'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'reason' => $validated['reason'],
                ],
                'status' => 'pending',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'reason' => $validated['reason'],
                'submitted_at' => now(),
            ]);

            // Create the leave request details
            $leaveRequest = LeaveRequest::create([
                'request_id' => $baseRequest->id,
                'leave_type' => $validated['leave_type'],
                'days_requested' => $validated['days_requested'],
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'medical_certificate' => $validated['medical_certificate'] ?? null,
                'is_paid' => $validated['is_paid'] ?? true,
            ]);

            // Load relationships
            $baseRequest->load(['user', 'leaveRequest']);

            return response()->json([
                'status' => 'success',
                'data' => $baseRequest,
                'message' => 'Leave request created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create leave request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $leaveRequest = BaseRequest::where('request_type', 'leave')
                ->where('id', $id)
                ->with(['user', 'leaveRequest', 'approvals.approver'])
                ->first();

            if (!$leaveRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave request not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $leaveRequest,
                'message' => 'Leave request retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve leave request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\LeaveRequestUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LeaveRequestUpdateRequest $request, $id)
    {
        try {
            $leaveRequest = BaseRequest::where('request_type', 'leave')
                ->where('id', $id)
                ->first();

            if (!$leaveRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave request not found'
                ], 404);
            }

            // Check if user can update this request
            if ($leaveRequest->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this request'
                ], 403);
            }

            // Only allow updates if status is pending
            if ($leaveRequest->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot update request that is not pending'
                ], 400);
            }

            $validated = $request->validated();

            // Update the base request
            $updateData = [];
            if (isset($validated['start_date'])) {
                $updateData['start_date'] = $validated['start_date'];
            }
            if (isset($validated['end_date'])) {
                $updateData['end_date'] = $validated['end_date'];
            }
            if (isset($validated['reason'])) {
                $updateData['reason'] = $validated['reason'];
            }

            if (!empty($updateData)) {
                $leaveRequest->update($updateData);
            }

            // Update the leave request details
            $leaveRequestDetailsUpdate = [];
            if (isset($validated['leave_type'])) {
                $leaveRequestDetailsUpdate['leave_type'] = $validated['leave_type'];
            }
            if (isset($validated['days_requested'])) {
                $leaveRequestDetailsUpdate['days_requested'] = $validated['days_requested'];
            }
            if (isset($validated['emergency_contact'])) {
                $leaveRequestDetailsUpdate['emergency_contact'] = $validated['emergency_contact'];
            }
            if (isset($validated['medical_certificate'])) {
                $leaveRequestDetailsUpdate['medical_certificate'] = $validated['medical_certificate'];
            }
            if (isset($validated['is_paid'])) {
                $leaveRequestDetailsUpdate['is_paid'] = $validated['is_paid'];
            }

            if (!empty($leaveRequestDetailsUpdate)) {
                $leaveRequest->leaveRequest->update($leaveRequestDetailsUpdate);
            }

            // Load relationships
            $leaveRequest->load(['user', 'leaveRequest']);

            return response()->json([
                'status' => 'success',
                'data' => $leaveRequest,
                'message' => 'Leave request updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update leave request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $leaveRequest = BaseRequest::where('request_type', 'leave')
                ->where('id', $id)
                ->first();

            if (!$leaveRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave request not found'
                ], 404);
            }

            // Check if user can delete this request
            if ($leaveRequest->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this request'
                ], 403);
            }

            // Only allow deletion if status is pending
            if ($leaveRequest->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete request that is not pending'
                ], 400);
            }

            // Delete the leave request details first
            $leaveRequest->leaveRequest->delete();

            // Delete the base request
            $leaveRequest->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave request deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete leave request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
