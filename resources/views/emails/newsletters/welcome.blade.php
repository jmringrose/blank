<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Newsletter</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>Welcome to Our Newsletter, {{ $record->name }}!</h1>
        <p>Thank you for subscribing to our photography newsletter. You'll receive weekly tips, tutorials, and inspiration to improve your photography skills.</p>
        <p>What to expect:</p>
        <ul>
            <li>Weekly photography tips and techniques</li>
            <li>Equipment reviews and recommendations</li>
            <li>Featured photographer spotlights</li>
            <li>Exclusive offers and workshops</li>
        </ul>
        <p>We're excited to have you join our community of passionate photographers!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>