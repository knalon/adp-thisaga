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
use App\Notifications\BidCancelled;
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

        // Create the appointment directly
        $appointment = new Appointment();
        $appointment->user_id = Auth::id();
        $appointment->car_id = $validated['car_id'];
        $appointment->appointment_date = $validated['appointment_date'];
        $appointment->notes = $validated['notes'] ?? null;
        $appointment->status = 'pending';
        $appointment->save();

        // Notify car owner and administrators about the new appointment
        $user = User::findOrFail(Auth::id());
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
        $appointment->bid_price = $validated['bid_price'];
        $appointment->save();

        // Create a new bid record
        $bid = new \App\Models\Bid();
        $bid->user_id = Auth::id();
        $bid->car_id = $car->id;
        $bid->appointment_id = $appointment->id;
        $bid->amount = $validated['bid_price'];
        $bid->status = 'pending';
        $bid->save();

        // Notify car owner and administrators about the new bid
        $user = User::findOrFail(Auth::id());
        $car->user->notify(new BidSubmitted($appointment, $car, $user));

        return redirect()->route('cars.show', $car->slug)->with('success', 'Bid submitted successfully.');
    }

    /**
     * Cancel a bid on an appointment
     */
    public function cancelBid(Appointment $appointment)
    {
        // Ensure user owns the appointment
        if (Auth::id() !== $appointment->user_id) {
            abort(403);
        }

        // Ensure the appointment has a bid
        if ($appointment->bid_price === null) {
            return back()->withErrors(['appointment' => 'This appointment does not have a bid to cancel.']);
        }

        // Check if there's already a transaction for this bid that's been finalized
        // If there is and it's been paid, the bid can't be cancelled
        if ($appointment->transaction && $appointment->transaction->status === 'paid') {
            return back()->withErrors(['appointment' => 'This bid cannot be cancelled as the transaction has already been completed.']);
        }

        // Get the car associated with the appointment
        $car = $appointment->car;
        $user = User::findOrFail(Auth::id());

        // Store the bid price before clearing it (for the notification)
        $previousBidPrice = $appointment->bid_price;

        // Remove the bid from the appointment
        $appointment->update([
            'bid_price' => null,
        ]);

        // If there was a pending transaction for this bid, delete it
        if ($appointment->transaction && $appointment->transaction->status === 'pending') {
            $appointment->transaction->delete();
        }

        // Notify car owner and administrators about the cancelled bid
        $car->user->notify(new BidCancelled($appointment, $car, $user, $previousBidPrice));

        // If this was an admin-approved bid, find alternative bids to present
        if ($appointment->bid_approved) {
            // Reset the bid_approved flag
            $appointment->update([
                'bid_approved' => false
            ]);

            // Reactivate car listing if it was marked as pending sale
            if ($car->is_pending_sale) {
                $car->update([
                    'is_pending_sale' => false
                ]);
            }
        }

        return redirect()->route('user.bids')->with('success', 'Bid cancelled successfully.');
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

    /**
     * Display the user's bids
     */
    public function userBids()
    {
        $user = User::findOrFail(Auth::id());
        $appointments = $user->appointments()
            ->with(['car', 'car.media', 'transaction'])
            ->whereNotNull('bid_price')
            ->latest()
            ->get();

        // Format the appointments and add media URL
        $appointments->transform(function ($appointment) {
            // Handle car media
            if ($appointment->car) {
                $appointment->car->images = $appointment->car->getMedia('car_images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                    ];
                });
            }
            return $appointment;
        });

        return Inertia::render('User/Bids', [
            'appointments' => $appointments,
        ]);
    }
}
