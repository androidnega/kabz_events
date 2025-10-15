<?php

namespace App\Notifications;

use App\Models\FeaturedAd;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeaturedAdApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ad;

    /**
     * Create a new notification instance.
     */
    public function __construct(FeaturedAd $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Get the notification's delivery channels.
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
                    ->subject('🎉 Your Featured Ad is Approved!')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Great news! Your featured ad has been approved and is now live.')
                    ->line('Ad Details:')
                    ->line('• Headline: ' . $this->ad->headline)
                    ->line('• Placement: ' . ucfirst($this->ad->placement))
                    ->line('• Started: ' . $this->ad->start_date->format('M d, Y'))
                    ->line('• Expires: ' . $this->ad->end_date->format('M d, Y'))
                    ->action('View Ad Performance', route('vendor.featured-ads.index'))
                    ->line('Your ad is now visible to potential clients!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Featured Ad Approved',
            'message' => 'Your featured ad "' . $this->ad->headline . '" has been approved and is now live.',
            'ad_id' => $this->ad->id,
            'placement' => $this->ad->placement,
            'action_url' => route('vendor.featured-ads.index'),
        ];
    }
}
