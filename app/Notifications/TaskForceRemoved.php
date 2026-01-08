<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskForceRemoved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $taskForceName;

    /**
     * Create a new notification instance.
     */
    public function __construct($taskForceName)
    {
        $this->taskForceName = $taskForceName;
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
            ->subject('Update on Task Force Assignment: ' . $this->taskForceName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been removed from the **' . $this->taskForceName . '** task force.')
            ->line('If you believe this is an error, please contact your Head of Department or PSM.')
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
