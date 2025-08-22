<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        {!! $emailContent !!}
        
        @if(!$hasUnsubscribe)
            <hr style="margin: 30px 0; border: none; border-top: 2px solid #333333;">
            <p style="font-size: 12px; color: #666;">
               Don't need these newsletters in your mailbox? Then let's get you out of here - <a href="{{ $unsubscribeUrl ?? '#' }}" target="_blank" style="color: #666;">Unsubscribe</a><br />
               If you feel these newsletters were sent to you in error, please contact us at <a href="mailto:info@realcoolphototours.com" style="color: #666;">RealCoolPhotoTours</a>
            </p>
        @endif
    </div>
</body>
</html>