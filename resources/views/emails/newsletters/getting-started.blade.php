<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Getting Started Guide</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>Getting Started Guide</h1>
        <p>Hi {{ $record->name }},</p>
        <p>Whether you're new to photography or looking to improve your skills, here's your essential getting started guide:</p>
        
        <h3>Camera Basics:</h3>
        <ul>
            <li>Learn the exposure triangle (aperture, shutter speed, ISO)</li>
            <li>Practice with different shooting modes</li>
            <li>Understand your camera's metering system</li>
        </ul>
        
        <h3>Composition Tips:</h3>
        <ul>
            <li>Rule of thirds</li>
            <li>Leading lines</li>
            <li>Framing and depth</li>
        </ul>
        
        <p>Next week: We'll dive deeper into aperture settings and depth of field!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>