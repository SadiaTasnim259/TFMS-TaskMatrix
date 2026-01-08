<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskForceRequestRejected extends Notification
{
    use Queueable;

    public $taskForce;
    public $user;
    public $remarks;

    public function __construct($taskForce, $user, $remarks)
    {
        $this->taskForce = $taskForce;
        $this->user = $user;
        $this->remarks = $remarks;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Membership Request Rejected: ' . $this->taskForce->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The membership request for **' . $this->user->name . '** has been **REJECTED**.')
            ->line('**Task Force:** ' . $this->taskForce->name)
            ->line('**Reason:** ' . $this->remarks)
            ->action('View Task Force', route('hod.task-forces.show', $this->taskForce->id));
    }
}
