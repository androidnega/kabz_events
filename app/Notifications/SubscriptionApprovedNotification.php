<?php

namespace App\Notifications;

use App\Models\VendorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(VendorSubscription $subscription)
    {
        $this->subscription = $subscription;
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
                    ->subject('🎉 Your Subscription is Approved!')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Great news! Your ' . $this->subscription->plan . ' subscription has been approved.')
                    ->line('Your subscription is now active and you can enjoy all the benefits.')
                    ->line('Subscription Details:')
                    ->line('• Plan: ' . $this->subscription->plan)
                    ->line('• Amount: GHS ' . number_format($this->subscription->price_amount, 2))
                    ->line('• Started: ' . $this->subscription->started_at->format('M d, Y'))
                    ->line('• Expires: ' . ($this->subscription->ends_at ? $this->subscription->ends_at->format('M d, Y') : 'Lifetime'))
                    ->action('View Subscriptions', route('vendor.subscriptions'))
                    ->line('Thank you for choosing KABZS EVENT!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Subscription Approved',
            'message' => 'Your ' . $this->subscription->plan . ' subscription has been approved and is now active.',
            'subscription_id' => $this->subscription->id,
            'plan' => $this->subscription->plan,
            'action_url' => route('vendor.subscriptions'),
        ];
    }
}
