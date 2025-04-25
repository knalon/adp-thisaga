<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserBanned extends Notification implements ShouldQueue
{
    use Queueable;

    private string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reason = '')
    {
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
        $contactUrl = route('contact');
        
        return (new MailMessage)
            ->subject('Your ABC Cars Account Has Been Suspended')
            ->greeting('Hello ' . $notifiable->name)
            ->line('We regret to inform you that your account on ABC Cars has been suspended.')
            ->when(!empty($this->reason), function ($message) {
                return $message->line('Reason: ' . $this->reason);
            })
            ->line('During this suspension, you will not be able to:')
            ->line('• List new vehicles')
            ->line('• Update existing listings')
            ->line('• Schedule appointments')
            ->line('• Submit bids')
            ->line('• Complete transactions')
            ->line('If you believe this was done in error or wish to appeal this decision, please contact our support team.')
            ->action('Contact Support', $contactUrl)
            ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account Suspended',
            'message' => 'Your account has been suspended. Please contact support for assistance.',
            'reason' => $this->reason,
            'contact_url' => route('contact'),
        ];
    }
}
