<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as BaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Search requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRequests(Request $request)
    {
        try {
            $query = $request->get('query');
            $type = $request->get('type');
            $status = $request->get('status');

            if (!$query) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search query is required'
                ], 400);
            }

            $searchQuery = BaseRequest::query();

            // Apply search query to multiple fields
            $searchQuery->where(function ($q) use ($query) {
                $q->where('reason', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                  });
            });

            // Apply type filter
            if ($type) {
                $searchQuery->where('request_type', $type);
            }

            // Apply status filter
            if ($status) {
                $searchQuery->where('status', $status);
            }

            // If user is not admin, only show their own requests or requests they can approve
            $user = Auth::user();
            if (!$user->hasRole('admin')) {
                $searchQuery->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('workflow.department', function ($q) use ($user) {
                          $q->whereHas('users', function ($q) use ($user) {
                              $q->where('id', $user->id);
                          });
                      });
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $requests = $searchQuery->with(['user', 'workflow'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $requests,
                'message' => 'Search results retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search requests',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get request history for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $query = BaseRequest::where('user_id', $user->id);

            // Apply filters
            if ($request->has('type')) {
                $query->where('request_type', $request->type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Apply date filters
            if ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->where('created_at', '<=', $request->end_date);
            }

            // Order by newest first
            $query->orderBy('created_at', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 15);
            $requests = $query->with(['workflow'])->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $requests,
                'message' => 'Request history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve request history',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
