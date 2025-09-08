@include('components.email-editor', [
    'type' => 'Newsletter',
    'stepLabel' => 'File',
    'orderLabel' => 'Order',
    'orderConstraints' => 'min="1"',
    'cancelRoute' => route('newsletter-steps.index'),
    'storeRoute' => route('newsletter-editor.store'),
    'updateRoute' => isset($step) ? route('newsletter-editor.update', $step->id) : null,
    'previewRoute' => isset($step) ? '/preview/newsletter/' . $step->order : null,
    'editorHeight' => 800
])
