<!-- resources/views/emails/verification_code.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
</head>

<body>
    <h1>Password Reset Code</h1>
    <p>Your password reset verification code is: <strong>{{ $verificationCode }}</strong></p>
    <p>Please enter this code to reset your password.</p>
</body>

</html>