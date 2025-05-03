<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function createAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $appointment = Appointment::create([
                'car_id' => $data['car_id'],
                'user_id' => $data['user_id'],
                'appointment_date' => $data['appointment_date'],
                'bid_amount' => $data['bid_amount'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Notify the car owner about the new appointment and bid
            $car = Car::find($data['car_id']);
            $car->user->notify(new \App\Notifications\NewAppointmentRequest($appointment));

            return $appointment;
        });
    }

    public function updateBid(Appointment $appointment, float $newBidAmount): Appointment
    {
        return DB::transaction(function () use ($appointment, $newBidAmount) {
            $appointment->update([
                'bid_amount' => $newBidAmount,
                'status' => 'pending', // Reset status to pending when bid is updated
            ]);

            // Notify the car owner about the updated bid
            $appointment->car->user->notify(new \App\Notifications\BidUpdated($appointment));

            return $appointment;
        });
    }

    public function approveAppointment(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => 'approved',
            ]);

            // Notify the user that their appointment was approved
            $appointment->user->notify(new \App\Notifications\AppointmentApproved($appointment));

            return $appointment;
        });
    }

    public function rejectAppointment(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => 'rejected',
            ]);

            // Notify the user that their appointment was rejected
            $appointment->user->notify(new \App\Notifications\AppointmentRejected($appointment));

            return $appointment;
        });
    }

    public function completeAppointment(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => 'completed',
            ]);

            // Notify both parties that the appointment is completed
            $appointment->user->notify(new \App\Notifications\AppointmentCompleted($appointment));
            $appointment->car->user->notify(new \App\Notifications\AppointmentCompleted($appointment));

            return $appointment;
        });
    }

    public function cancelAppointment(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => 'cancelled',
            ]);

            // Notify both parties that the appointment is cancelled
            $appointment->user->notify(new \App\Notifications\AppointmentCancelled($appointment));
            $appointment->car->user->notify(new \App\Notifications\AppointmentCancelled($appointment));

            return $appointment;
        });
    }

    public function getHighestBid(Car $car): ?float
    {
        return Appointment::where('car_id', $car->id)
            ->where('status', '!=', 'cancelled')
            ->max('bid_amount');
    }

    public function getUpcomingAppointments(User $user): array
    {
        $now = Carbon::now();

        return [
            'as_buyer' => Appointment::where('user_id', $user->id)
                ->where('appointment_date', '>', $now)
                ->where('status', 'approved')
                ->with('car')
                ->get(),
            'as_seller' => Appointment::whereHas('car', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('appointment_date', '>', $now)
                ->where('status', 'approved')
                ->with(['car', 'user'])
                ->get(),
        ];
    }
} 