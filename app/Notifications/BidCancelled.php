<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Car;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public Car $car,
        public User $user,
        public ?float $previousBidPrice
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bid Cancelled')
            ->line('A bid has been cancelled for your car listing.')
            ->line('Car: ' . $this->car->title)
            ->line('Previous Bid Amount: $' . number_format($this->previousBidPrice, 2))
            ->line('Cancelled by: ' . $this->user->name)
            ->action('View Car Listing', route('cars.show', $this->car->slug))
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->car->id,
            'user_id' => $this->user->id,
            'previous_bid_price' => $this->previousBidPrice,
            'message' => 'A bid has been cancelled for your car listing.',
        ];
    }
}
