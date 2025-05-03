<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test Drive Appointment Rejected')
            ->line('Unfortunately, your test drive appointment has been rejected.')
            ->line("Car: {$this->appointment->car->title}")
            ->line("Date: {$this->appointment->appointment_date->format('F j, Y g:i A')}")
            ->line("Bid Amount: $" . number_format($this->appointment->bid_amount, 2))
            ->action('View Other Cars', url('/cars'))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->appointment->car_id,
            'user_id' => $this->appointment->user_id,
            'type' => 'appointment_rejected',
            'message' => "Test drive appointment rejected for {$this->appointment->car->title}",
        ];
    }
} 