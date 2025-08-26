<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as BaseRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $user = Auth::user();

            // Get user's requests statistics
            $userRequests = BaseRequest::where('user_id', $user->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get pending approvals for the user
            $pendingApprovals = $this->getPendingApprovalsCount($user);

            // Get unread notifications count
            $unreadNotifications = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            // Get department statistics if user is admin or manager
            $departmentStats = $this->getDepartmentStats($user);

            $stats = [
                'user_requests' => [
                    'pending' => $userRequests['pending'] ?? 0,
                    'approved' => $userRequests['approved'] ?? 0,
                    'rejected' => $userRequests['rejected'] ?? 0,
                    'total' => $userRequests->sum(),
                ],
                'pending_approvals' => $pendingApprovals,
                'unread_notifications' => $unreadNotifications,
                'department_stats' => $departmentStats,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard statistics',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get analytics data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(Request $request)
    {
        try {
            $user = Auth::user();

            // Get request trends
            $requestTrends = $this->getRequestTrends($request);

            // Get approval statistics
            $approvalStats = $this->getApprovalStats($user);

            // Get department performance if user is admin
            $departmentPerformance = $this->getDepartmentPerformance($user);

            $analytics = [
                'request_trends' => $requestTrends,
                'approval_statistics' => $approvalStats,
                'department_performance' => $departmentPerformance,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics,
                'message' => 'Analytics data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve analytics data',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get pending approvals count for a user.
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    private function getPendingApprovalsCount($user)
    {
        // This would use the ApprovalService to get pending requests
        // For now, we'll return a placeholder value
        return 0;
    }

    /**
     * Get department statistics.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getDepartmentStats($user)
    {
        // This would get department-specific statistics
        // For now, we'll return a placeholder
        return [
            'total_users' => 0,
            'active_requests' => 0,
        ];
    }

    /**
     * Get request trends.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function getRequestTrends($request)
    {
        // Get request trends for the last 30 days
        $startDate = now()->subDays(30);

        $trends = BaseRequest::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            'request_type'
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date', 'request_type')
        ->orderBy('date')
        ->get()
        ->groupBy('request_type');

        return $trends;
    }

    /**
     * Get approval statistics.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getApprovalStats($user)
    {
        // Get approval statistics for the user
        $approvals = DB::table('request_approvals')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->where('approver_id', $user->id)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'approved' => $approvals['approved'] ?? 0,
            'rejected' => $approvals['rejected'] ?? 0,
            'pending' => $approvals['pending'] ?? 0,
        ];
    }

    /**
     * Get department performance statistics.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getDepartmentPerformance($user)
    {
        // This would get department performance data
        // For now, we'll return a placeholder
        return [
            'average_processing_time' => 0,
            'approval_rate' => 0,
        ];
    }
}
