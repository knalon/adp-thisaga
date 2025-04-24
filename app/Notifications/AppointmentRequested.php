<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentRequested extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $car;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, Car $car, User $user)
    {
        $this->appointment = $appointment;
        $this->car = $car;
        $this->user = $user;
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
        return (new MailMessage)
            ->subject('New Test Drive Appointment Request')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new test drive appointment has been requested for your car:')
            ->line('Car: ' . $this->car->year . ' ' . $this->car->make . ' ' . $this->car->model)
            ->line('Requested by: ' . $this->user->name)
            ->line('Date: ' . $this->appointment->appointment_date->format('F j, Y, g:i a'))
            ->line('Notes: ' . ($this->appointment->notes ?? 'No notes provided'))
            ->action('View Appointment Details', url('/appointments/' . $this->appointment->id))
            ->line('Thank you for using ABC Used Cars!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->car->id,
            'user_id' => $this->user->id,
            'message' => 'New appointment request for ' . $this->car->year . ' ' . $this->car->make . ' ' . $this->car->model,
        ];
    }
}
