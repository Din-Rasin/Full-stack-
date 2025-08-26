<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WorkflowStoreRequest;
use App\Http\Requests\Api\WorkflowUpdateRequest;
use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
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
            $query = Workflow::query();

            // Apply filters
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $workflows = $query->with(['department', 'creator'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $workflows,
                'message' => 'Workflows retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve workflows',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\WorkflowStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WorkflowStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $workflow = Workflow::create(array_merge(
                $validated,
                [
                    'approval_steps' => json_decode($validated['approval_steps'], true),
                    'conditions' => isset($validated['conditions']) ? json_decode($validated['conditions'], true) : null,
                    'created_by' => auth('sanctum')->user()->id,
                ]
            ));

            // Load relationships
            $workflow->load(['department', 'creator']);

            return response()->json([
                'status' => 'success',
                'data' => $workflow,
                'message' => 'Workflow created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create workflow',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\WorkflowUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WorkflowUpdateRequest $request, $id)
    {
        try {
            $workflow = Workflow::findOrFail($id);

            $validated = $request->validated();

            $updateData = $validated;

            if (isset($validated['approval_steps'])) {
                $updateData['approval_steps'] = json_decode($validated['approval_steps'], true);
            }

            if (isset($validated['conditions'])) {
                $updateData['conditions'] = $validated['conditions'] ? json_decode($validated['conditions'], true) : null;
            }

            $workflow->update($updateData);

            // Load relationships
            $workflow->load(['department', 'creator']);

            return response()->json([
                'status' => 'success',
                'data' => $workflow,
                'message' => 'Workflow updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update workflow',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
