<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;
    protected $car;
    protected $user;
    protected $previousBidPrice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, Car $car, User $user, float $previousBidPrice)
    {
        $this->appointment = $appointment;
        $this->car = $car;
        $this->user = $user;
        $this->previousBidPrice = $previousBidPrice;
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
            ->subject('Bid Cancelled on ' . $this->car->title)
            ->line($this->user->name . ' has cancelled their bid of $' . number_format($this->previousBidPrice, 2) . ' on your car: ' . $this->car->title)
            ->line('Appointment date: ' . $this->appointment->appointment_date->format('F j, Y, g:i a'))
            ->action('View Car Listing', url('/cars/' . $this->car->slug))
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'previous_bid_price' => $this->previousBidPrice,
            'message' => $this->user->name . ' has cancelled their bid of $' . number_format($this->previousBidPrice, 2) . ' on ' . $this->car->title,
        ];
    }
}
