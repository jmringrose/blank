<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        @php
            // Convert placeholder variables to PHP format
            $convertedContent = preg_replace('/VAR_(firstName|lastName|name|email|currentStep|unsubscribeUrl)_VAR/', '{{ $$1 }}', $emailContent);
        @endphp
        {!! $convertedContent !!}
    </div>
</body>
</html>
