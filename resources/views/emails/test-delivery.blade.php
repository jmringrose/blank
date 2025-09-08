<!doctype html-->
<html lang="en"-->
<head-->
    <meta charset="utf-8" /-->
    <meta name="viewport" content="width=device-width,initial-scale=1" /-->
    <title-->Delivery Test</title-->
    <style-->
        body { margin:0; padding:0; background:#f6f8fb; color:#222; }
        .wrapper { width:100%; background:#f6f8fb; padding:24px 0; }
        .container { max-width:600px; margin:0 auto; background:#ffffff; padding:24px; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; line-height:1.55; }
        .muted { color:#666; font-size:12px; }
        .footer { margin-top:24px; border-top:1px solid #eee; padding-top:16px; }
        .sr-only { position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden; }
    </style-->
</head-->
<body-->
<!-- Preheader text (hidden in body, shown in inbox preview) ---->
<div class="sr-only"-->Quick check to confirm you’re getting our emails.</div-->

<div class="wrapper"-->
    <div class="container"-->
        <p-->Hi {{ $recipientName }},</p-->
        <p-->This is a quick delivery test from {{ config('app.name') }} to confirm you can receive our emails.</p-->
        <p-->Please reply to this message with <strong-->“Received”</strong--> when it arrives.</p-->

        <div class="footer"-->
            <p class="muted"-->
                Sent by {{ config('app.name') }}<br-->
                If anything looks off (sender name or formatting), please mention it in your reply.
            </p-->
        </div-->
    </div-->
</div-->
</body-->
</html-->
