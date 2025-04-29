<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class TransactionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Transaction $transaction;
    protected ?Appointment $appointment;

    public function __construct(Transaction $transaction, ?Appointment $appointment = null)
    {
        $this->transaction = $transaction;
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $car = $this->transaction->car;

        return (new MailMessage)
            ->subject('Transaction Completed - ' . $car->make . ' ' . $car->model)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your transaction has been finalized.')
            ->line('Car: ' . $car->make . ' ' . $car->model . ' (' . $car->registration_year . ')')
            ->line('Amount: $' . number_format($this->transaction->amount, 2))
            ->line('Status: ' . ucfirst($this->transaction->status))
            ->line('Payment Date: ' . $this->transaction->payment_date->format('Y-m-d H:i'))
            ->action('View Transaction Details', url('/user/my-transactions/' . $this->transaction->id))
            ->line('Thank you for using ABC Used Cars!');
    }

    public function toDatabase($notifiable): array
    {
        $car = $this->transaction->car;

        return [
            'title' => 'Transaction Completed',
            'icon' => 'heroicon-o-banknotes',
            'body' => 'Your transaction for ' . $car->make . ' ' . $car->model . ' has been finalized.',
            'transaction_id' => $this->transaction->id,
            'car_id' => $this->transaction->car_id,
            'amount' => $this->transaction->amount,
        ];
    }

    public static function make(Transaction $transaction, ?Appointment $appointment = null): self
    {
        $car = $transaction->car;

        // Send a Filament notification in the UI
        FilamentNotification::make()
            ->title('Transaction Completed')
            ->body("Your transaction for {$car->make} {$car->model} has been finalized for $" . number_format($transaction->amount, 2))
            ->success()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url("/user/my-transactions/{$transaction->id}"),
            ])
            ->sendToDatabase($transaction->user);

        return new static($transaction, $appointment);
    }
}
