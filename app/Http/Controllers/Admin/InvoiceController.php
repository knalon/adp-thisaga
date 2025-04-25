<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate PDF invoice for a transaction
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice(Transaction $transaction)
    {
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