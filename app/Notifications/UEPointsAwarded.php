<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UEPointsAwarded extends Notification
{
    use Queueable;

    protected $pointsAwarded;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $pointsAwarded, string $reason = 'Administrative award')
    {
        $this->pointsAwarded = $pointsAwarded;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('UEPoints Awarded!')
                    ->greeting('Hello '.$notifiable->name.'!')
                    ->line('Congratulations! You have been awarded '.$this->pointsAwarded.' UEPoints.')
                    ->line('Reason: '.$this->reason)
                    ->action('View Your UEPoints', url('/uepoints'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'points_awarded' => $this->pointsAwarded,
            'reason' => $this->reason,
            'current_total' => $notifiable->ue_points
        ];
    }
}