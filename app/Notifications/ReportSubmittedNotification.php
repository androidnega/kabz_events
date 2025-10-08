<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReportSubmittedNotification extends Notification
{
    use Queueable;

    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reporter = $this->report->reporter ?? $this->report->user;
        
        return (new MailMessage)
                    ->subject('New Report Submitted - KABZS Event')
                    ->greeting('Hello Admin!')
                    ->line('A new report has been submitted and requires your attention.')
                    ->line('**Report Details:**')
                    ->line('Reporter: ' . $reporter->name)
                    ->line('Category: ' . $this->report->category)
                    ->line('Type: ' . ucfirst($this->report->target_type ?? $this->report->type))
                    ->line('Message: ' . Str::limit($this->report->message, 100))
                    ->action('View Report', route('admin.reports.show', $this->report->id))
                    ->line('Thank you for keeping KABZS Event safe! 🇬🇭');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reporter = $this->report->reporter ?? $this->report->user;
        
        return [
            'report_id' => $this->report->id,
            'reporter_name' => $reporter->name,
            'category' => $this->report->category,
            'type' => $this->report->target_type ?? $this->report->type,
            'message' => 'New report submitted by ' . $reporter->name,
            'action_url' => route('admin.reports.show', $this->report->id),
        ];
    }
}
