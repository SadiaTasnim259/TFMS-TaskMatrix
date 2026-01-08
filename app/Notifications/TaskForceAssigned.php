<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskForceAssigned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $taskForce;
    public $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($taskForce, $role)
    {
        $this->taskForce = $taskForce;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Force Assignment: ' . $this->taskForce->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned to the **' . $this->taskForce->name . '** task force.')
            ->line('**Role:** ' . $this->role)
            ->action('View Task Force', route('workload.index')) // Redirect to their dashboard/workload
            ->line('Thank you for your contribution!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
