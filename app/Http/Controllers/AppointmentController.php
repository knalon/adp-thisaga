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
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

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

    public function store(Request $request, Car $car): JsonResponse
    {
        $request->validate([
            'appointment_date' => 'required|date|after:now',
            'bid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment = $this->appointmentService->createAppointment([
            'car_id' => $car->id,
            'user_id' => Auth::id(),
            'appointment_date' => $request->appointment_date,
            'bid_amount' => $request->bid_amount,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment->load('car', 'user'),
        ]);
    }

    public function updateBid(Request $request, Car $car): JsonResponse
    {
        $request->validate([
            'bid_amount' => 'required|numeric|min:0',
        ]);

        $appointment = $car->appointments()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment = $this->appointmentService->updateBid($appointment, $request->bid_amount);

        return response()->json([
            'message' => 'Bid updated successfully',
            'appointment' => $appointment->load('car', 'user'),
        ]);
    }

    public function getHighestBid(Car $car): JsonResponse
    {
        $highestBid = $this->appointmentService->getHighestBid($car);

        return response()->json([
            'highest_bid' => $highestBid,
        ]);
    }

    public function getUserAppointments(): JsonResponse
    {
        $appointments = $this->appointmentService->getUpcomingAppointments(Auth::user());

        return response()->json([
            'appointments' => $appointments,
        ]);
    }

    public function cancel(Car $car): JsonResponse
    {
        $appointment = $car->appointments()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment = $this->appointmentService->cancelAppointment($appointment);

        return response()->json([
            'message' => 'Appointment cancelled successfully',
            'appointment' => $appointment->load('car', 'user'),
        ]);
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
