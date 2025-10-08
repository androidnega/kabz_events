<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification
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
        return (new MailMessage)
                    ->subject('Your Report Has Been Resolved - KABZS Event')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Thank you for reporting an issue on KABZS Event.')
                    ->line('We wanted to let you know that your report has been reviewed and resolved.')
                    ->line('**Report Details:**')
                    ->line('Category: ' . $this->report->category)
                    ->line('Submitted on: ' . $this->report->created_at->format('F j, Y'))
                    ->line('Resolved on: ' . $this->report->resolved_at->format('F j, Y'))
                    ->when($this->report->admin_note, function ($mail) {
                        return $mail->line('**Admin Note:** ' . $this->report->admin_note);
                    })
                    ->line('Thank you for helping us maintain a safe and trustworthy platform! 🇬🇭')
                    ->line('If you have any questions, please feel free to contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'category' => $this->report->category,
            'message' => 'Your report has been resolved',
            'resolved_at' => $this->report->resolved_at,
        ];
    }
}
