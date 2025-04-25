<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class PaymentController extends Controller
{
    /**
     * Process a payment for a transaction
     *
     * @param \Illuminate\Http\Request $request
     * @param int $transaction
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function process(Request $request, $transaction)
    {
        $transaction = Transaction::findOrFail($transaction);

        // Check if the transaction belongs to the authenticated user
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the transaction is already paid
        if ($transaction->status !== 'pending') {
            return redirect()->route('filament.user.resources.transactions.index')
                ->with('error', 'This transaction has already been processed.');
        }

        // In a real application, we would integrate with a payment gateway here
        // For demo purposes, we're just showing a payment form

        return view('payments.process', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Simulate a successful payment
     *
     * @param \Illuminate\Http\Request $request
     * @param int $transaction
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, $transaction)
    {
        $transaction = Transaction::findOrFail($transaction);

        // Update transaction status
        $transaction->update([
            'status' => 'paid',
            'transaction_reference' => 'PAY-' . Str::upper(Str::random(12)),
        ]);

        // Update car status
        if ($transaction->car) {
            $transaction->car->update([
                'is_active' => false,
                'sold_at' => now(),
            ]);
        }

        // Redirect back to transactions
        return redirect()->route('filament.user.resources.transactions.index')
            ->with('success', 'Payment processed successfully!');
    }

    /**
     * Handle a canceled payment
     *
     * @param \Illuminate\Http\Request $request
     * @param int $transaction
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, $transaction)
    {
        return redirect()->route('filament.user.resources.transactions.index')
            ->with('info', 'Payment was canceled.');
    }
}
