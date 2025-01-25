<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGroupInvitationNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $body;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param string $title
     * @param string $body
     * @param User $user
     */
    public function __construct($title, $body, User $user)
    {
        $this->title = $title;
        $this->body = $body;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Use the NotificationService to send the notification
        $notificationService = app(NotificationService::class);
        $notificationService->sendNotificationToUser($this->title, $this->body, $this->user);
    }
}
