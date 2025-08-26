<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel;
use App\Models\RequestApproval;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get request statistics
        $pendingRequests = RequestModel::where('status', 'pending')->count();
        $approvedRequests = RequestModel::where('status', 'approved')->count();
        $rejectedRequests = RequestModel::where('status', 'rejected')->count();

        // Get user-specific statistics
        $userPendingRequests = RequestModel::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $userApprovedRequests = RequestModel::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $userRejectedRequests = RequestModel::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        // Get pending approvals for the user
        $pendingApprovals = RequestApproval::where('approver_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get recent notifications
        $recentNotifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get requests by type
        $leaveRequests = RequestModel::where('request_type', 'leave')->count();
        $missionRequests = RequestModel::where('request_type', 'mission')->count();

        return view('dashboard', compact(
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'userPendingRequests',
            'userApprovedRequests',
            'userRejectedRequests',
            'pendingApprovals',
            'recentNotifications',
            'leaveRequests',
            'missionRequests'
        ));
    }
}
