<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AppointmentRequested;
use App\Notifications\BidSubmitted;
use App\Notifications\AppointmentStatusChanged;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $appointments = $user->appointments()
            ->with(['car', 'car.user'])
            ->latest()
            ->get();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    public function create(Car $car)
    {
        if (!$car->is_approved || !$car->is_active) {
            abort(404);
        }

        return Inertia::render('Appointments/Create', [
            'car' => $car,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if (!$car->is_approved || !$car->is_active) {
            abort(404);
        }

        // Check if user is not trying to book their own car
        if (Auth::id() === $car->user_id) {
            return back()->withErrors(['car_id' => 'You cannot book an appointment for your own car.']);
        }

        // Check if user already has an appointment for this car
        $existingAppointment = Appointment::where('user_id', Auth::id())
            ->where('car_id', $car->id)
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['car_id' => 'You already have an appointment for this car.']);
        }

        $user = User::findOrFail(Auth::id());
        $appointment = $user->appointments()->create([
            'car_id' => $validated['car_id'],
            'appointment_date' => $validated['appointment_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        // Notify car owner and administrators about the new appointment
        $car->user->notify(new AppointmentRequested($appointment, $car, $user));

        return redirect()->route('cars.show', $car->slug)->with('success', 'Test drive appointment request submitted successfully.');
    }

    public function submitBid(Request $request, Appointment $appointment)
    {
        // Ensure user owns the appointment
        if (Auth::id() !== $appointment->user_id) {
            abort(403);
        }

        // Ensure appointment status is appropriate for bidding
        if ($appointment->status !== 'approved') {
            return back()->withErrors(['appointment' => 'You can only submit bids for approved test drive appointments.']);
        }

        $validated = $request->validate([
            'bid_price' => 'required|numeric|min:0',
        ]);

        // Get the car associated with the appointment
        $car = $appointment->car;

        // Check if the bid is higher than the current highest bid
        $currentHighestBid = $car->appointments()
            ->where('status', 'approved')
            ->whereNotNull('bid_price')
            ->max('bid_price');

        if ($currentHighestBid && $validated['bid_price'] <= $currentHighestBid) {
            return back()->withErrors(['bid_price' => 'Your bid must be higher than the current highest bid of $' . number_format($currentHighestBid, 2)]);
        }

        // Update the appointment with the bid
        $appointment->update([
            'bid_price' => $validated['bid_price'],
        ]);

        // Notify car owner and administrators about the new bid
        $user = User::findOrFail(Auth::id());
        $car->user->notify(new BidSubmitted($appointment, $car, $user));

        return redirect()->route('cars.show', $car->slug)->with('success', 'Bid submitted successfully.');
    }

    public function userAppointments()
    {
        $user = User::findOrFail(Auth::id());
        $appointments = $user->appointments()
            ->with(['car', 'car.user'])
            ->latest()
            ->get();

        return Inertia::render('User/Appointments', [
            'appointments' => $appointments,
        ]);
    }
}
