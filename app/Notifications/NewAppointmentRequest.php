<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewAppointmentRequest extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Appointment Request')
            ->line('You have received a new appointment request.')
            ->line('Car: ' . $this->appointment->car->title)
            ->line('Date: ' . $this->appointment->appointment_date)
            ->action('View Appointment', url('/admin/appointments/' . $this->appointment->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->appointment->car_id,
            'user_id' => $this->appointment->user_id,
            'appointment_date' => $this->appointment->appointment_date,
        ];
    }
} 