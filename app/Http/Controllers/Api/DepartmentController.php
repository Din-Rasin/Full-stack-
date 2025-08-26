<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DepartmentStoreRequest;
use App\Http\Requests\Api\DepartmentUpdateRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
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
            $query = Department::query();

            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $departments = $query->with(['manager', 'users'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $departments,
                'message' => 'Departments retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve departments',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\DepartmentStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $department = Department::create($validated);

            // Load relationships
            $department->load(['manager']);

            return response()->json([
                'status' => 'success',
                'data' => $department,
                'message' => 'Department created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create department',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\DepartmentUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentUpdateRequest $request, $id)
    {
        try {
            $department = Department::findOrFail($id);

            $validated = $request->validated();

            $department->update($validated);

            // Load relationships
            $department->load(['manager']);

            return response()->json([
                'status' => 'success',
                'data' => $department,
                'message' => 'Department updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update department',
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
            $department = Department::findOrFail($id);

            // Check if department has users
            if ($department->users()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete department with users'
                ], 400);
            }

            $department->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Department deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete department',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
