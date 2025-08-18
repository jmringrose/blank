<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Newsletter (Copy)</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        Hello {{ $firstName }},
Your email is {{ $email }}
This is step {{ $currentStep }}
<a href="{{ $unsubscribeUrl }}">Unsubscribe</a>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>