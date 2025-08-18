<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Photography Tours</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>Welcome to Photography Tours, {{ $record->name }}!</h1>
        <p>Thank you for joining our photography community. We're excited to share amazing Costa Rica photography opportunities with you.</p>
        <p>Over the next few days, you'll receive tips and insights about photographing Costa Rica's incredible wildlife and landscapes.</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>