<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionFinalized extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Transaction $transaction)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Transaction Finalized')
            ->line('Your transaction has been finalized.')
            ->line('Transaction ID: ' . $this->transaction->id)
            ->line('Amount: $' . $this->transaction->amount)
            ->line('Status: ' . ucfirst($this->transaction->status))
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
            ->line('Thank you for using our service!');
    }

    public function toArray($notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'status' => $this->transaction->status,
        ];
    }
}
