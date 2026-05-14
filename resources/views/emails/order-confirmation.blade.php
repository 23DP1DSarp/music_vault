<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333;">Hello, {{ $details['name'] }}!</h2>
        <p>Welcome to our application. We are excited to have you onboard.</p>
        <p><strong>Message:</strong> {{ $details['message'] }}</p>
    </div>
</body>
</html>