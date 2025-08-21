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
            {!! config('email-templates.unsubscribe_footer') !!}
        @endif
    </div>
</body>
</html>