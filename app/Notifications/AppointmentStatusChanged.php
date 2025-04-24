<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $car;
    protected $previousStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, Car $car, string $previousStatus)
    {
        $this->appointment = $appointment;
        $this->car = $car;
        $this->previousStatus = $previousStatus;
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
        $url = url("/appointments");
        $statusVerb = $this->getStatusVerb();

        $mail = (new MailMessage)
                    ->subject('Test Drive Appointment Status Update')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your test drive appointment for ' . $this->car->make . ' ' . $this->car->model . ' has been ' . $statusVerb . '.')
                    ->line('Appointment date: ' . $this->appointment->appointment_date->format('F j, Y \a\t g:i A'))
                    ->action('View Appointment Details', $url);

        if ($this->appointment->status === 'approved') {
            $mail->line('You can now submit a bid for this car.');
        } elseif ($this->appointment->status === 'rejected') {
            $mail->line('If you have any questions, please contact our support team.');
        } elseif ($this->appointment->status === 'completed') {
            $mail->line('Thank you for using our car sales platform!');
        }

        return $mail;
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
            'previous_status' => $this->previousStatus,
            'current_status' => $this->appointment->status,
            'message' => 'Your test drive appointment for ' . $this->car->make . ' ' . $this->car->model . ' has been ' . $this->getStatusVerb() . '.',
            'type' => 'appointment_status_changed',
        ];
    }

    /**
     * Get the appropriate verb for the status.
     */
    private function getStatusVerb(): string
    {
        switch ($this->appointment->status) {
            case 'approved':
                return 'approved';
            case 'rejected':
                return 'rejected';
            case 'completed':
                return 'marked as completed';
            default:
                return 'updated';
        }
    }
}
