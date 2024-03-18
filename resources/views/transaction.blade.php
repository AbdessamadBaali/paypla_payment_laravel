<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Transaction</title>
</head>
<body>
    <h1>Initiate PayPal Transaction</h1>

    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <form action="{{ route('processTransaction') }}" method="GET">
        @csrf
        <button type="submit">Initiate Payment</button>
    </form>

</body>
</html>
