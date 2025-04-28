<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $car;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, Car $car)
    {
        $this->appointment = $appointment;
        $this->car = $car;
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
            ->subject('Your Bid Has Been Approved')
            ->line('Great news! Your bid of $' . number_format($this->appointment->bid_price, 2) . ' for ' . $this->car->title . ' has been approved.')
            ->line('Please complete the purchase to secure the vehicle.')
            ->action('Complete Purchase', url('/user/bids'))
            ->line('Thank you for using ABC Used Car Portal!');
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
            'car_title' => $this->car->title,
            'bid_price' => $this->appointment->bid_price,
            'message' => 'Your bid of $' . number_format($this->appointment->bid_price, 2) . ' for ' . $this->car->title . ' has been approved.',
        ];
    }
}
