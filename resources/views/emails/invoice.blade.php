<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
</head>

<body>
    <h1>Your Payment Receipt For Subscription</h1>
    <p>Thank you for your payment!</p>
    <p><strong>Amount:</strong> {{ $data['amount'] }} {{ strtoupper($data['currency']) }}</p> <!-- Use $data -->
    <p>If you have any questions, feel free to contact us.</p>
</body>

</html>