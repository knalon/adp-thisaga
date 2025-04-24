<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\TransactionFinalized;

class TransactionController extends Controller
{
    public function finalize(Request $request, Appointment $appointment)
    {
        // Check if appointment is approved and has a bid
        if ($appointment->status !== 'approved' || $appointment->bid_price === null) {
            return back()->withErrors(['appointment' => 'Cannot finalize this appointment.']);
        }

        // Check if car is still active and approved
        $car = $appointment->car;
        if (!$car->is_active || !$car->is_approved) {
            return back()->withErrors(['car' => 'This car is no longer available.']);
        }

        $validated = $request->validate([
            'final_price' => 'required|numeric|min:0',
        ]);

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => $appointment->user_id,
            'car_id' => $car->id,
            'appointment_id' => $appointment->id,
            'final_price' => $validated['final_price'],
            'status' => 'completed',
            'transaction_reference' => 'TR-' . Str::upper(Str::random(10)),
        ]);

        // Mark appointment as completed
        $appointment->update(['status' => 'completed']);

        // Deactivate the car
        $car->update(['is_active' => false]);

        // Notify the buyer
        $appointment->user->notify(new TransactionFinalized($transaction, $car));

        // Also notify the seller
        $car->user->notify(new TransactionFinalized($transaction, $car));

        return redirect()->route('admin.transactions')->with('success', 'Transaction finalized successfully.');
    }

    public function userTransactions()
    {
        $user = User::findOrFail(Auth::id());
        $transactions = $user->transactions()
            ->with(['car', 'appointment'])
            ->latest()
            ->get();

        return Inertia::render('User/Transactions', [
            'transactions' => $transactions,
        ]);
    }
}
