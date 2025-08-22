@php
    $footerTemplate = config('emails.templates.unsubscribe_footer');
    $footerHtml = \Illuminate\Support\Facades\Blade::render($footerTemplate, ['unsubscribeUrl' => $unsubscribeUrl ?? '#']);
@endphp
{!! $footerHtml !!}