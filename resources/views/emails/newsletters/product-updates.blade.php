<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Updates</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c5aa0;">Latest Product Updates</h1>
    
    <p>Hello {{ $firstName }},</p>
    
    <p>We've been busy improving your experience! Here's what's new:</p>
    
    <div style="border-left: 4px solid #28a745; padding-left: 20px; margin: 20px 0;">
        <h3 style="color: #28a745; margin-top: 0;">✨ New Feature: Smart Dashboard</h3>
        <p>Our new dashboard automatically organizes your most important information and shows personalized insights based on your usage patterns.</p>
    </div>
    
    <div style="border-left: 4px solid #17a2b8; padding-left: 20px; margin: 20px 0;">
        <h3 style="color: #17a2b8; margin-top: 0;">🔧 Improvement: Faster Loading</h3>
        <p>We've optimized our servers and reduced page load times by 40%. You should notice everything feels much snappier!</p>
    </div>
    
    <div style="border-left: 4px solid #ffc107; padding-left: 20px; margin: 20px 0;">
        <h3 style="color: #e67e22; margin-top: 0;">🐛 Bug Fix: Mobile Navigation</h3>
        <p>Fixed the issue where the mobile menu wouldn't close properly on some devices. Thanks for your patience!</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2c5aa0;">🔮 Coming Soon</h3>
        <p style="margin-bottom: 0;">Dark mode, advanced filtering, and team collaboration features are in development. Stay tuned!</p>
    </div>
    
    <p>As always, we'd love to hear your feedback. What would you like to see next?</p>
    
    <p style="margin-top: 30px;">
        Keep innovating,<br>
        The Product Team
    </p>
    
    <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
    <p style="font-size: 12px; color: #666;">
        <a href="{{ $unsubscribeUrl }}" style="color: #666;">Unsubscribe</a> from this newsletter
    </p>
</body>
</html>