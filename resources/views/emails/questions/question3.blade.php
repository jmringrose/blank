<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather and Clothing Recommendations</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px;">
        <h1 style="color: #2c3e50; font-size: 24px; margin-bottom: 20px;">Weather and Clothing Recommendations</h1>
        
        <p style="font-size: 16px; line-height: 1.6; color: #333;">Hi {{ $firstName }},</p>
        
        <p style="font-size: 16px; line-height: 1.6; color: #333;">
            Here's what to expect weather-wise and how to dress for your photography tour:
        </p>
        
        <h2 style="color: #34495e; font-size: 20px; margin: 20px 0 10px 0;">Layering is Key</h2>
        <p style="font-size: 16px; line-height: 1.6; color: #333;">
            Weather can change quickly in many photography locations. We recommend:
        </p>
        <ul style="font-size: 16px; line-height: 1.6; color: #333;">
            <li>Base layer: Moisture-wicking thermal underwear</li>
            <li>Insulating layer: Fleece or down jacket</li>
            <li>Outer layer: Waterproof and windproof shell</li>
            <li>Extra layers you can add or remove as needed</li>
        </ul>
        
        <h2 style="color: #34495e; font-size: 20px; margin: 20px 0 10px 0;">Footwear</h2>
        <ul style="font-size: 16px; line-height: 1.6; color: #333;">
            <li>Waterproof hiking boots with good ankle support</li>
            <li>Warm, moisture-wicking socks (bring extras)</li>
            <li>Gaiters for muddy or snowy conditions</li>
            <li>Microspikes or crampons if icy conditions expected</li>
        </ul>
        
        <h2 style="color: #34495e; font-size: 20px; margin: 20px 0 10px 0;">Accessories</h2>
        <ul style="font-size: 16px; line-height: 1.6; color: #333;">
            <li>Warm hat that covers your ears</li>
            <li>Sun hat with brim for bright conditions</li>
            <li>Insulated gloves (thin liner gloves for camera operation)</li>
            <li>Neck gaiter or scarf</li>
            <li>Quality sunglasses</li>
        </ul>
        
        <h2 style="color: #34495e; font-size: 20px; margin: 20px 0 10px 0;">Weather Considerations</h2>
        <ul style="font-size: 16px; line-height: 1.6; color: #333;">
            <li>Early morning shoots can be very cold</li>
            <li>Temperatures can drop significantly at altitude</li>
            <li>Weather can change rapidly in mountainous areas</li>
            <li>Rain and snow are always possible</li>
        </ul>
        
        <p style="font-size: 16px; line-height: 1.6; color: #333;">
            We'll provide specific weather forecasts and recommendations closer to your tour date. Remember, there's no bad weather, only inappropriate clothing!
        </p>
        
        <p style="font-size: 16px; line-height: 1.6; color: #333;">
            Stay warm and dry!<br>
            The Real Cool Photo Tours Team
        </p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666; text-align: center;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a> from question emails
        </p>
    </div>
</body>
</html>