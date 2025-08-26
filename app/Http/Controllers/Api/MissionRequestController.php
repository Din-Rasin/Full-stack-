<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MissionRequestStoreRequest;
use App\Http\Requests\Api\MissionRequestUpdateRequest;
use App\Models\MissionRequest;
use App\Models\Request as BaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionRequestController extends Controller
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
            $query = BaseRequest::where('request_type', 'mission');

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
            $missionRequests = $query->with(['user', 'missionRequest'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $missionRequests,
                'message' => 'Mission requests retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve mission requests',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\MissionRequestStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MissionRequestStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            // Create the base request
            $baseRequest = BaseRequest::create([
                'request_type' => 'mission',
                'user_id' => Auth::id(),
                'workflow_id' => 1, // This should be determined by the workflow service
                'request_data' => [
                    'destination' => $validated['destination'],
                    'purpose' => $validated['purpose'],
                    'estimated_cost' => $validated['estimated_cost'] ?? null,
                    'transportation_mode' => $validated['transportation_mode'] ?? null,
                    'accommodation_details' => $validated['accommodation_details'] ?? null,
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

            // Create the mission request details
            $missionRequest = MissionRequest::create([
                'request_id' => $baseRequest->id,
                'destination' => $validated['destination'],
                'purpose' => $validated['purpose'],
                'estimated_cost' => $validated['estimated_cost'] ?? null,
                'transportation_mode' => $validated['transportation_mode'] ?? null,
                'accommodation_details' => $validated['accommodation_details'] ?? null,
                'budget_approved' => false,
            ]);

            // Load relationships
            $baseRequest->load(['user', 'missionRequest']);

            return response()->json([
                'status' => 'success',
                'data' => $baseRequest,
                'message' => 'Mission request created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create mission request',
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
            $missionRequest = BaseRequest::where('request_type', 'mission')
                ->where('id', $id)
                ->with(['user', 'missionRequest', 'approvals.approver'])
                ->first();

            if (!$missionRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mission request not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $missionRequest,
                'message' => 'Mission request retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve mission request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\MissionRequestUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MissionRequestUpdateRequest $request, $id)
    {
        try {
            $missionRequest = BaseRequest::where('request_type', 'mission')
                ->where('id', $id)
                ->first();

            if (!$missionRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mission request not found'
                ], 404);
            }

            // Check if user can update this request
            if ($missionRequest->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this request'
                ], 403);
            }

            // Only allow updates if status is pending
            if ($missionRequest->status !== 'pending') {
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
                $missionRequest->update($updateData);
            }

            // Update the mission request details
            $missionRequestDetailsUpdate = [];
            if (isset($validated['destination'])) {
                $missionRequestDetailsUpdate['destination'] = $validated['destination'];
            }
            if (isset($validated['purpose'])) {
                $missionRequestDetailsUpdate['purpose'] = $validated['purpose'];
            }
            if (isset($validated['estimated_cost'])) {
                $missionRequestDetailsUpdate['estimated_cost'] = $validated['estimated_cost'];
            }
            if (isset($validated['transportation_mode'])) {
                $missionRequestDetailsUpdate['transportation_mode'] = $validated['transportation_mode'];
            }
            if (isset($validated['accommodation_details'])) {
                $missionRequestDetailsUpdate['accommodation_details'] = $validated['accommodation_details'];
            }

            if (!empty($missionRequestDetailsUpdate)) {
                $missionRequest->missionRequest->update($missionRequestDetailsUpdate);
            }

            // Load relationships
            $missionRequest->load(['user', 'missionRequest']);

            return response()->json([
                'status' => 'success',
                'data' => $missionRequest,
                'message' => 'Mission request updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update mission request',
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
            $missionRequest = BaseRequest::where('request_type', 'mission')
                ->where('id', $id)
                ->first();

            if (!$missionRequest) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mission request not found'
                ], 404);
            }

            // Check if user can delete this request
            if ($missionRequest->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete this request'
                ], 403);
            }

            // Only allow deletion if status is pending
            if ($missionRequest->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete request that is not pending'
                ], 400);
            }

            // Delete the mission request details first
            $missionRequest->missionRequest->delete();

            // Delete the base request
            $missionRequest->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Mission request deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete mission request',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
