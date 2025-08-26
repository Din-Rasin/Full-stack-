<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessWorkflowApproval implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $request;
    protected $approver;
    protected $status;
    protected $comments;

    /**
     * Create a new job instance.
     */
    public function __construct($request, $approver, $status, $comments = null)
    {
        $this->request = $request;
        $this->approver = $approver;
        $this->status = $status;
        $this->comments = $comments;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Process the workflow approval
        $approvalService = app(\App\Services\ApprovalService::class);
        $approvalService->processApproval($this->request, $this->approver, $this->status, $this->comments);
    }
}
