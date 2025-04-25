<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            margin: 0;
            color: #0066CC;
            font-size: 24px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info-block {
            flex: 1;
        }
        .invoice-info-block h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .invoice-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-total {
            text-align: right;
            margin-top: 30px;
        }
        .invoice-total h3 {
            margin: 0;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>{{ $companyName }}</h1>
        <p>{{ $companyAddress }}</p>
        <p>{{ $companyPhone }} | {{ $companyEmail }}</p>
    </div>
    
    <div class="invoice-info">
        <div class="invoice-info-block">
            <h3>Invoice To:</h3>
            <p>
                <strong>{{ $customer->name }}</strong><br>
                Email: {{ $customer->email }}<br>
                @if($customer->phone)
                Phone: {{ $customer->phone }}<br>
                @endif
            </p>
        </div>
        
        <div class="invoice-info-block">
            <h3>Invoice Details:</h3>
            <p>
                <strong>Invoice Number:</strong> {{ $invoiceNumber }}<br>
                <strong>Invoice Date:</strong> {{ $invoiceDate->format('M d, Y') }}<br>
                <strong>Payment Status:</strong> {{ ucfirst($transaction->status) }}<br>
                @if($transaction->payment_method)
                <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}<br>
                @endif
                @if($transaction->transaction_id)
                <strong>Transaction ID:</strong> {{ $transaction->transaction_id }}<br>
                @endif
            </p>
        </div>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Details</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($car)
                    {{ $car->make }} {{ $car->model }} ({{ $car->year }})
                    @else
                    Vehicle Purchase
                    @endif
                </td>
                <td>
                    @if($car)
                    Color: {{ $car->color }}<br>
                    @if($car->mileage)
                    Mileage: {{ $car->mileage }} miles<br>
                    @endif
                    @if($appointment)
                    Appointment Date: {{ $appointment->appointment_date->format('M d, Y') }}
                    @endif
                    @else
                    Appointment ID: {{ $appointment->id ?? 'N/A' }}
                    @endif
                </td>
                <td>${{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="invoice-total">
        <h3>Total: ${{ number_format($transaction->amount, 2) }}</h3>
    </div>
    
    @if($transaction->notes)
    <div style="margin-top: 30px;">
        <h3>Notes:</h3>
        <p>{{ $transaction->notes }}</p>
    </div>
    @endif
    
    <div class="invoice-footer">
        <p>Thank you for your business with {{ $companyName }}.</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>
</html> 