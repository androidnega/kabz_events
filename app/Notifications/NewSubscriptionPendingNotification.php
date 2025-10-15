<?php

namespace App\Notifications;

use App\Models\VendorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubscriptionPendingNotification extends Notification implements ShouldQueue
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
        $vendor = $this->subscription->vendor;
        
        return (new MailMessage)
                    ->subject('New Subscription Payment Pending Approval')
                    ->greeting('Hello Admin!')
                    ->line('A vendor has completed payment for a subscription and is awaiting approval.')
                    ->line('Vendor: ' . $vendor->business_name)
                    ->line('Plan: ' . $this->subscription->plan)
                    ->line('Amount: GHS ' . number_format((float)($this->subscription->price_amount ?? 0), 2))
                    ->line('Payment Method: ' . ucfirst($this->subscription->payment_method ?? 'Paystack'))
                    ->line('Paid At: ' . $this->subscription->paid_at->format('M d, Y h:i A'))
                    ->action('Review Subscription', route('admin.subscriptions.pending'))
                    ->line('Please review and approve within 24 hours or it will be auto-approved.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Subscription Pending',
            'message' => $this->subscription->vendor->business_name . ' paid for ' . $this->subscription->plan . ' subscription',
            'subscription_id' => $this->subscription->id,
            'vendor_id' => $this->subscription->vendor_id,
            'plan' => $this->subscription->plan,
            'amount' => $this->subscription->price_amount,
            'action_url' => route('admin.subscriptions.pending'),
        ];
    }
}
