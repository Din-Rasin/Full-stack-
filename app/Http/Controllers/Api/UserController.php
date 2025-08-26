<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
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
            $query = User::query();

            // Apply filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->with(['roles', 'departments'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\UserStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'employee_id' => $validated['employee_id'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Assign departments
            if (isset($validated['department_ids'])) {
                $user->departments()->attach($validated['department_ids']);
            }

            // Assign roles
            if (isset($validated['role_ids'])) {
                $user->roles()->attach($validated['role_ids']);
            }

            // Load relationships
            $user->load(['roles', 'departments']);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Api\UserUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validated();

            // Update user details
            $user->update($validated);

            // Update password if provided
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $user->save();
            }

            // Sync departments
            if (isset($validated['department_ids'])) {
                $user->departments()->sync($validated['department_ids']);
            }

            // Sync roles
            if (isset($validated['role_ids'])) {
                $user->roles()->sync($validated['role_ids']);
            }

            // Load relationships
            $user->load(['roles', 'departments']);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user',
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
            $user = User::findOrFail($id);

            // Prevent deleting the current user
            if ($user->id === auth('sanctum')->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot delete yourself'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Assign a role to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'role_id' => 'required|exists:roles,id',
            ]);

            $user = User::findOrFail($validated['user_id']);
            $role = Role::findOrFail($validated['role_id']);

            // Attach the role to the user
            $user->roles()->attach($role->id, [
                'assigned_at' => now(),
                'assigned_by' => auth('sanctum')->user()->id
            ]);

            // Load relationships
            $user->load(['roles', 'departments']);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'Role assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to assign role',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get all roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles()
    {
        try {
            $roles = Role::all();

            return response()->json([
                'status' => 'success',
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve roles',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
