<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bid Updated')
            ->line('The bid for your car has been updated.')
            ->line('New Bid Amount: $' . number_format($this->appointment->bid_amount, 2))
            ->line('Appointment Date: ' . $this->appointment->appointment_date->format('F j, Y g:i A'))
            ->action('View Appointment', url('/appointments/' . $this->appointment->id))
            ->line('Please review the updated bid.');
    }

    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->appointment->car_id,
            'user_id' => $this->appointment->user_id,
            'bid_amount' => $this->appointment->bid_amount,
            'appointment_date' => $this->appointment->appointment_date,
            'message' => 'Bid has been updated'
        ];
    }
}
