<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action
     */
    public function logAction(User $user = null, $action, $tableName, $recordId = null, $oldValues = [], $newValues = [], $ipAddress = null)
    {
        // If no user provided, use the currently authenticated user
        if (!$user && Auth::check()) {
            $user = Auth::user();
        }

        // If no IP address provided, get the current request IP
        if (!$ipAddress) {
            $ipAddress = Request::ip();
        }

        return AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Log a create action
     */
    public function logCreate(User $user = null, $tableName, $recordId, $newValues = [], $ipAddress = null)
    {
        return $this->logAction($user, 'create', $tableName, $recordId, [], $newValues, $ipAddress);
    }

    /**
     * Log an update action
     */
    public function logUpdate(User $user = null, $tableName, $recordId, $oldValues = [], $newValues = [], $ipAddress = null)
    {
        return $this->logAction($user, 'update', $tableName, $recordId, $oldValues, $newValues, $ipAddress);
    }

    /**
     * Log a delete action
     */
    public function logDelete(User $user = null, $tableName, $recordId, $oldValues = [], $ipAddress = null)
    {
        return $this->logAction($user, 'delete', $tableName, $recordId, $oldValues, [], $ipAddress);
    }

    /**
     * Get audit logs for a specific user
     */
    public function getLogsForUser(User $user)
    {
        return AuditLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get audit logs for a specific table
     */
    public function getLogsForTable($tableName)
    {
        return AuditLog::where('table_name', $tableName)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get audit logs for a specific record
     */
    public function getLogsForRecord($tableName, $recordId)
    {
        return AuditLog::where('table_name', $tableName)->where('record_id', $recordId)->orderBy('created_at', 'desc')->get();
    }
}
