<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;

class AppointmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;
    protected string $previousStatus;

    public function __construct(Appointment $appointment, string $previousStatus)
    {
        $this->appointment = $appointment;
        $this->previousStatus = $previousStatus;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Appointment Status Updated')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your appointment status has been updated from ' . $this->previousStatus . ' to ' . $this->appointment->status . '.');

        // Different lines depending on the status
        if ($this->appointment->status === 'approved') {
            $message->line('Great news! Your appointment has been approved. Please make sure to attend at the scheduled time.')
                   ->line('Date and Time: ' . $this->appointment->appointment_date->format('F j, Y, g:i a'));
        } elseif ($this->appointment->status === 'rejected') {
            $message->line('Unfortunately, your appointment request has been rejected. You might want to try scheduling at a different time.');
        } elseif ($this->appointment->status === 'completed') {
            $message->line('Your appointment has been marked as completed. We hope you had a great experience!');
        }

        return $message
            ->action('View Appointment', url('/dashboard/appointments/' . $this->appointment->id))
            ->line('Thank you for using our service!');
    }

    public function toDatabase($notifiable): array
    {
        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'canceled' => 'Canceled',
        ];

        $previousStatusLabel = $statusLabels[$this->previousStatus] ?? $this->previousStatus;
        $currentStatusLabel = $statusLabels[$this->appointment->status] ?? $this->appointment->status;

        return [
            'appointment_id' => $this->appointment->id,
            'car_id' => $this->appointment->car_id,
            'message' => "Appointment status changed from {$previousStatusLabel} to {$currentStatusLabel}",
            'url' => '/dashboard/appointments/' . $this->appointment->id,
        ];
    }

    public static function make(Appointment $appointment, string $previousStatus): self
    {
        // Also send a Filament notification in the UI
        FilamentNotification::make()
            ->title('Appointment Status Updated')
            ->body("Your appointment status has been updated from {$previousStatus} to {$appointment->status}.")
            ->success()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url("/dashboard/appointments/{$appointment->id}"),
            ])
            ->sendToDatabase($appointment->user);

        return new static($appointment, $previousStatus);
    }
}
