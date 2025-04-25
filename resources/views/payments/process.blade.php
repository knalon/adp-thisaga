<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Payment Gateway</h1>
            <p class="text-gray-600">Complete your purchase</p>
        </div>

        <div class="border-b border-gray-200 pb-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Order Summary</h2>
            <div class="mt-2 flex justify-between">
                <span class="text-gray-600">{{ $transaction->car->make }} {{ $transaction->car->model }}</span>
                <span class="font-medium">${{ number_format($transaction->final_price, 2) }}</span>
            </div>
        </div>

        <form action="{{ route('payment.success', $transaction->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiry" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="cvv" class="block text-sm font-medium text-gray-700">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Pay ${{ number_format($transaction->final_price, 2) }}
                </button>

                <a href="{{ route('payment.cancel', $transaction->id) }}" class="block text-center mt-3 text-sm text-gray-600 hover:text-gray-800">
                    Cancel Payment
                </a>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-gray-500">
            <p>This is a simulation. No actual payment will be processed.</p>
        </div>
    </div>
</body>
</html>
