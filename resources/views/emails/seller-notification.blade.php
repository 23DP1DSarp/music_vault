<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seller Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333;">Sveiki, {{ $details['name'] }}!</h2>
        <p><strong>Ziņojums:</strong> {{ $details['message'] }}</p>
    </div>
</body>
</html>