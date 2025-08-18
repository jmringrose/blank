<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>What Makes Us Different</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>What Makes Us Different</h1>
        <p>Hi {{ $record->name }},</p>
        <p>Here's what sets our photography tours apart:</p>
        <ul>
            <li><strong>Small Groups:</strong> Maximum 6 photographers per tour</li>
            <li><strong>Local Expertise:</strong> Native guides who know the best spots</li>
            <li><strong>Professional Instruction:</strong> Learn from award-winning photographers</li>
            <li><strong>All Skill Levels:</strong> From beginners to professionals</li>
            <li><strong>Sustainable Tourism:</strong> Supporting local communities</li>
        </ul>
        <p>Ready to capture Costa Rica's magic? Our next tour starts soon!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>