<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $subject;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $subject, $message)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send email notification
        // In a real application, you would use Laravel's Mail facade here
        // For now, we'll just log that the email would be sent
        Log::info("Email notification sent to {$this->user->email}: {$this->subject}");
    }
}
