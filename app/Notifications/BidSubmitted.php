<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BidSubmitted extends Notification implements ShouldQueue
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
        $url = url("/admin/appointments");

        return (new MailMessage)
                    ->subject('New Bid Submitted')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line($this->user->name . ' has submitted a bid of $' . number_format($this->appointment->bid_price, 2) . '.')
                    ->line('Car: ' . $this->car->make . ' ' . $this->car->model . ' (' . $this->car->registration_year . ')')
                    ->action('Review Bid', $url)
                    ->line('Thank you for using our car sales platform!');
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
            'bid_price' => $this->appointment->bid_price,
            'message' => 'New bid of $' . number_format($this->appointment->bid_price, 2) . ' for ' . $this->car->make . ' ' . $this->car->model,
            'type' => 'bid_submitted',
        ];
    }
}
