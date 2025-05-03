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
            ->subject('Bid Updated for Test Drive Appointment')
            ->line('A bid has been updated for your car listing.')
            ->line("Car: {$this->appointment->car->title}")
            ->line("Buyer: {$this->appointment->user->name}")
            ->line("New Bid Amount: $" . number_format($this->appointment->bid_amount, 2))
            ->action('View Appointment', url('/admin/appointments/' . $this->appointment->id))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->appointment->car_id,
            'user_id' => $this->appointment->user_id,
            'type' => 'bid_updated',
            'message' => "Bid updated for {$this->appointment->car->title}",
        ];
    }
} 