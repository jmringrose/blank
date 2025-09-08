@php
    // Convert placeholder variables to PHP format
    $convertedContent = preg_replace('/VAR_(firstName|lastName|email|currentStep|unsubscribeUrl)_VAR/', '{{ $$1 }}', $emailContent);

    // Check if content is already a complete HTML document
    if (strpos($convertedContent, '<!doctype') !== false || strpos($convertedContent, '<!DOCTYPE') !== false) {
        // Content is already complete HTML, output as-is
        echo $convertedContent;
    } else {
        // Content needs wrapper
@endphp

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <style>
        @media screen and (max-width: 610px) {
            .sm-px { padding-left: 4px !important; padding-right: 4px !important; }
            .sm-center { text-align: left !important; }
            .sm-block { display: block !important; width: 100% !important; }
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.4; color: #333; margin:0; padding:0; background:#eff1f3;" class="sm-block">
<div style="max-width: 620px; margin: 0 auto; padding: 4px; background-color: #fffcfa;" class="sm-px">
    XXXX

    {!! $convertedContent !!}
</div>
</body>
</html>
@php
    }
@endphp
