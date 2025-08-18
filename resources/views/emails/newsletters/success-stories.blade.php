<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Success Stories</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1>Customer Success Stories</h1>
        <p>Hi {{ $record->name }},</p>
        <p>We love celebrating the amazing achievements of our photography community! Here are some inspiring success stories from fellow photographers:</p>
        
        <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h3>Sarah M. - Wildlife Photography</h3>
            <p><em>"After taking the Costa Rica tour, I finally captured the perfect shot of a quetzal in flight. The techniques I learned about patience and positioning made all the difference!"</em></p>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h3>Mike R. - Landscape Photography</h3>
            <p><em>"The workshop on long exposure techniques transformed my waterfall photography. I've since had three images accepted into a local gallery exhibition!"</em></p>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px;">
            <h3>Lisa K. - Portrait Photography</h3>
            <p><em>"Learning about natural light and posing has helped me start my own portrait business. I'm now booking clients regularly!"</em></p>
        </div>
        
        <p>Have a success story to share? Reply to this email - we'd love to feature you next month!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        <p style="font-size: 12px; color: #666;">
            <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>