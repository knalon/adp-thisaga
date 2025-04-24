<?php

namespace App\Notifications;

use App\Models\Car;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TransactionFinalized extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $car;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, Car $car)
    {
        $this->transaction = $transaction;
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
        $isBuyer = $notifiable->id === $this->transaction->user_id;
        $subject = $isBuyer ? 'Your Car Purchase is Complete' : 'Your Car Has Been Sold';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($isBuyer
                ? 'Congratulations! Your car purchase has been finalized.'
                : 'Congratulations! Your car has been sold.')
            ->line('Car: ' . $this->car->year . ' ' . $this->car->make . ' ' . $this->car->model)
            ->line('Final Price: $' . number_format($this->transaction->final_price, 2))
            ->line('Transaction Reference: ' . $this->transaction->transaction_reference)
            ->action('View Transaction Details', url('/user/transactions'))
            ->line('Thank you for using ABC Used Cars!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isBuyer = $notifiable->id === $this->transaction->user_id;

        return [
            'transaction_id' => $this->transaction->id,
            'car_id' => $this->car->id,
            'message' => $isBuyer
                ? 'Your purchase of ' . $this->car->year . ' ' . $this->car->make . ' ' . $this->car->model . ' has been finalized'
                : 'Your ' . $this->car->year . ' ' . $this->car->make . ' ' . $this->car->model . ' has been sold',
        ];
    }
}
