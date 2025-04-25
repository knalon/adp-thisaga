<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\RolesEnum;
use Spatie\Permission\Models\Role;
use App\Notifications\AppointmentStatusChanged;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalCars' => Car::count(),
            'pendingApprovals' => Car::where('is_approved', false)->count(),
            'pendingAppointments' => Appointment::where('status', 'pending')->count(),
            'completedTransactions' => Transaction::where('status', 'completed')->count(),
        ];

        $recentCars = Car::with('user')->latest()->take(5)->get();
        $recentAppointments = Appointment::with(['user', 'car'])->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentCars' => $recentCars,
            'recentAppointments' => $recentAppointments,
        ]);
    }

    public function users()
    {
        $users = User::with('roles')->paginate(15);
        $roles = Role::all();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles($validated['role']);

        return back()->with('success', 'Role assigned successfully.');
    }

    public function cars()
    {
        $pendingCars = Car::where('is_approved', false)
            ->with('user')
            ->latest()
            ->paginate(10);

        $approvedCars = Car::where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Cars', [
            'pendingCars' => $pendingCars,
            'approvedCars' => $approvedCars,
        ]);
    }

    public function approveCar(Car $car)
    {
        $car->update(['is_approved' => true]);

        return back()->with('success', 'Car listing approved successfully.');
    }

    public function rejectCar(Car $car)
    {
        $car->delete();

        return back()->with('success', 'Car listing rejected and removed.');
    }

    public function appointments()
    {
        $pendingAppointments = Appointment::where('status', 'pending')
            ->with(['user', 'car', 'car.user'])
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        $approvedAppointments = Appointment::where('status', 'approved')
            ->with(['user', 'car', 'car.user'])
            ->latest()
            ->paginate(10, ['*'], 'approved_page');

        $rejectedAppointments = Appointment::where('status', 'rejected')
            ->with(['user', 'car', 'car.user'])
            ->latest()
            ->paginate(10, ['*'], 'rejected_page');
        
        // Get appointments with bids for admin review
        $biddedAppointments = Appointment::where('status', 'approved')
            ->whereNotNull('bid_price')
            ->with(['user', 'car', 'car.user'])
            ->orderBy('bid_price', 'desc')
            ->paginate(10, ['*'], 'bidded_page');

        return Inertia::render('Admin/Appointments', [
            'pendingAppointments' => $pendingAppointments,
            'approvedAppointments' => $approvedAppointments,
            'rejectedAppointments' => $rejectedAppointments,
            'biddedAppointments' => $biddedAppointments,
        ]);
    }

    public function approveAppointment(Appointment $appointment)
    {
        $previousStatus = $appointment->status;
        $appointment->update(['status' => 'approved']);
        
        // Notify the user about the appointment approval
        $appointment->user->notify(new AppointmentStatusChanged($appointment, $previousStatus));

        return back()->with('success', 'Appointment approved successfully.');
    }

    public function rejectAppointment(Appointment $appointment)
    {
        $previousStatus = $appointment->status;
        $appointment->update(['status' => 'rejected']);
        
        // Notify the user about the appointment rejection
        $appointment->user->notify(new AppointmentStatusChanged($appointment, $previousStatus));

        return back()->with('success', 'Appointment rejected.');
    }

    public function approveBid(Appointment $appointment)
    {
        // Check if appointment is approved and has a bid
        if ($appointment->status !== 'approved' || $appointment->bid_price === null) {
            return back()->withErrors(['appointment' => 'Cannot approve this bid.']);
        }
        
        return Inertia::render('Admin/FinalizeBid', [
            'appointment' => $appointment->load(['user', 'car', 'car.user']),
        ]);
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'car'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Transactions', [
            'transactions' => $transactions,
        ]);
    }
}
 