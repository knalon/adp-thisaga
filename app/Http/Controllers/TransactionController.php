<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\TransactionFinalized;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function finalize(Request $request, Appointment $appointment)
    {
        // Check if the appointment is already finalized
        if ($appointment->transaction()->exists()) {
            return back()->with('error', 'This appointment already has a transaction.');
        }

        // Validate the request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Create the transaction
        $transaction = new Transaction();
        $transaction->user_id = $appointment->user_id;
        $transaction->car_id = $appointment->car_id;
        $transaction->appointment_id = $appointment->id;
        $transaction->amount = $validated['amount'];
        $transaction->status = 'pending';
        $transaction->save();

        // Mark the car as pending sale
        $car = $appointment->car;
        $car->is_pending_sale = true;
        $car->save();

        // Mark the bid as approved
        $appointment->bid_approved = true;
        $appointment->save();

        // Log the activity
        ActivityLog::log(
            'Created transaction',
            'transaction_create',
            $transaction,
            [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'user_id' => $transaction->user_id,
            ]
        );

        // Notify the buyer
        $appointment->user->notify(new TransactionFinalized($transaction, $car));

        return back()->with('success', 'Transaction finalized successfully.');
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

    public function generateInvoice(Transaction $transaction)
    {
        // Check if the user is authorized to view this invoice
        if (Auth::id() !== $transaction->user_id && !Auth::user()->roles->pluck('name')->contains('admin')) {
            abort(403, 'Unauthorized');
        }

        $data = [
            'transaction' => $transaction,
            'car' => $transaction->appointment->car,
            'seller' => $transaction->appointment->car->user,
            'buyer' => $transaction->user,
            'date' => now()->format('F d, Y'),
            'invoice_number' => 'INV-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = PDF::loadView('invoices.transaction', $data);

        return $pdf->download('invoice-' . $data['invoice_number'] . '.pdf');
    }

    public function markAsPaid(Request $request, Transaction $transaction)
    {
        // Check if the user is authorized
        if (Auth::id() !== $transaction->user_id && !Auth::user()->roles->pluck('name')->contains('admin')) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,bank_transfer,check,other',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        // Update the transaction
        $transaction->status = 'paid';
        $transaction->payment_method = $validated['payment_method'];
        $transaction->transaction_id = $validated['transaction_id'] ?? null;
        $transaction->payment_date = now();
        $transaction->save();

        // Update car status to sold
        $car = $transaction->car;
        $car->is_active = false;
        $car->is_sold = true;
        $car->is_pending_sale = false;
        $car->sold_at = now();
        $car->save();

        // Delete other appointments and bids for this car
        // First get the current appointment ID to exclude it
        $currentAppointmentId = $transaction->appointment_id;

        // Delete other bids for this car
        \App\Models\Bid::where('car_id', $car->id)
            ->whereNot('appointment_id', $currentAppointmentId)
            ->delete();

        // Delete other appointments for this car
        Appointment::where('car_id', $car->id)
            ->whereNot('id', $currentAppointmentId)
            ->delete();

        // Log the activity
        ActivityLog::log(
            'Marked transaction as paid',
            'transaction_paid',
            $transaction,
            [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'user_id' => $transaction->user_id,
            ]
        );

        return back()->with('success', 'Payment recorded successfully.');
    }
}
