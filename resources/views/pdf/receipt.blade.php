<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body>
    <h1>Invoice #{{ $invoiceNumber }}</h1>
    <p>Date: {{ $date }}</p>
    <p>Client Name: {{ $clientName }}</p>
    <p>Amount: ${{ $amount }}</p>
</body>
</html>
