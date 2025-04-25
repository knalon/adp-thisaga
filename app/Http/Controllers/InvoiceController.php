<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function generateInvoice(Transaction $transaction)
    {
        // Check if user has permission to view this invoice
        if (!Auth::check()) {
            abort(401, 'Unauthenticated');
        }
        
        $user = Auth::user();
        // Allow either the transaction owner or an admin to access
        if ($user->id !== $transaction->user_id && !$user->roles->pluck('name')->contains('admin')) {
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
} 