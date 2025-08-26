<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogAuditTrail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $action;
    protected $tableName;
    protected $recordId;
    protected $oldValues;
    protected $newValues;
    protected $ipAddress;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $action, $tableName, $recordId = null, $oldValues = [], $newValues = [], $ipAddress = null)
    {
        $this->user = $user;
        $this->action = $action;
        $this->tableName = $tableName;
        $this->recordId = $recordId;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log the audit trail
        $auditService = app(\App\Services\AuditService::class);
        $auditService->logAction($this->user, $this->action, $this->tableName, $this->recordId, $this->oldValues, $this->newValues, $this->ipAddress);
    }
}
