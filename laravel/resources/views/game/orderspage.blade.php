<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi Midtrans</title>
</head>
<body>
    <div class="container">
        <h1>Data Transaksi Midtrans</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (is_array($transactions))
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction['order_id'] }}</td>
                            <td>{{ $transaction['transaction_status'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2">{{ $transactions }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
