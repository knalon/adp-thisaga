<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }
        .invoice-box table tr.top td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td:last-child {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .mt-0 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <h1 class="mb-0">BC Cars Pte Ltd</h1>
                                <p class="mt-0">Used Car Sales Portal</p>
                            </td>
                            <td class="text-right">
                                <h2 class="mb-0">INVOICE</h2>
                                <p class="mt-0">{{ $invoice_number }}</p>
                                <p>Date: {{ $date }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Seller:</strong><br>
                                {{ $seller->name }}<br>
                                {{ $seller->email }}<br>
                                {{ $seller->phone ?? 'N/A' }}
                            </td>
                            <td class="text-right">
                                <strong>Buyer:</strong><br>
                                {{ $buyer->name }}<br>
                                {{ $buyer->email }}<br>
                                {{ $buyer->phone ?? 'N/A' }}<br>
                                {{ $buyer->address ?? '' }}<br>
                                {{ $buyer->city ?? '' }}, {{ $buyer->state ?? '' }} {{ $buyer->zip_code ?? '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td colspan="2">Transaction Details</td>
                <td>Payment Method</td>
                <td class="text-right">Status</td>
            </tr>
            <tr class="details">
                <td colspan="2">Transaction #{{ $transaction->id }}</td>
                <td>{{ ucfirst($transaction->payment_method ?? 'N/A') }}</td>
                <td class="text-right">{{ ucfirst($transaction->status) }}</td>
            </tr>
            <tr class="heading">
                <td>Car Details</td>
                <td>Year</td>
                <td>Condition</td>
                <td class="text-right">Price</td>
            </tr>
            <tr class="item">
                <td>{{ $car->make }} {{ $car->model }}</td>
                <td>{{ $car->year }}</td>
                <td>{{ ucfirst($car->condition) }}</td>
                <td class="text-right">${{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td colspan="3"></td>
                <td class="text-right">Total: ${{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 40px;">
            <h3>Additional Information</h3>
            <p><strong>Car Specifications</strong></p>
            <table>
                <tr>
                    <td width="25%"><strong>Color:</strong> {{ $car->color }}</td>
                    <td width="25%"><strong>Mileage:</strong> {{ number_format($car->mileage) }} miles</td>
                    <td width="25%"><strong>Transmission:</strong> {{ ucfirst($car->transmission) }}</td>
                    <td width="25%"><strong>Fuel Type:</strong> {{ ucfirst($car->fuel_type) }}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 40px;">
            <p>Thank you for your business with BC Cars Pte Ltd.</p>
            <p>This is an automatically generated invoice and does not require a signature.</p>
        </div>
    </div>
</body>
</html> 