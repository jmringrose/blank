<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $firstName }}, Welcome to Our Newsletter!</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">Welcome {{ $firstName }}!</h1>
        
        <p>Thank you for subscribing to our newsletter. We're excited to have you on board!</p>
        
        <p>Over the next few days, you'll receive valuable content including:</p>
        <ul>
            <li>Getting started guides</li>
            <li>Tips and tricks</li>
            <li>Advanced features</li>
            <li>And much more!</li>
        </ul>
        
        <p>If you have any questions, feel free to reply to this email.</p>
        
        <p>Best regards,<br>The Team</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            You're receiving this because you subscribed to our newsletter.
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>