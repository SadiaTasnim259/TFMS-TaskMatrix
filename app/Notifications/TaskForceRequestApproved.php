<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskForceRequestApproved extends Notification
{
    use Queueable;

    public $taskForce;
    public $user;
    public $role;

    public function __construct($taskForce, $user, $role)
    {
        $this->taskForce = $taskForce;
        $this->user = $user;
        $this->role = $role;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Membership Request Approved: ' . $this->taskForce->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The membership request for **' . $this->user->name . '** has been **APPROVED**.')
            ->line('**Task Force:** ' . $this->taskForce->name)
            ->line('**Role:** ' . $this->role)
            ->line('The member has been successfully added to the task force.')
            ->action('View Task Force', route('hod.task-forces.show', $this->taskForce->id));
    }
}
