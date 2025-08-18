<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Last Chance Offer</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>Last Chance Offer</h1>
        <p>Hi {{ $record->name }},</p>
        <p><strong>This is your final opportunity</strong> to join our exclusive Costa Rica Photography Tour at the early bird price!</p>
        
        <div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0;">
            <h3 style="margin-top: 0;">Special Offer Expires Tonight!</h3>
            <ul>
                <li>Save $500 on your tour package</li>
                <li>Free airport transfers included</li>
                <li>Bonus wildlife photography workshop</li>
            </ul>
        </div>
        
        <p>Don't miss out on the photography adventure of a lifetime!</p>
        <p><a href="https://www.realcoolphototours.com" style="background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">Book Your Tour Now</a></p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>