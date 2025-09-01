@include('components.email-editor', [
    'type' => 'Marketing',
    'stepLabel' => 'Step',
    'orderLabel' => 'Step Number',
    'orderConstraints' => 'min="1" max="6"',
    'cancelRoute' => route('marketing-steps.index'),
    'storeRoute' => route('marketing-editor.store'),
    'updateRoute' => isset($step) ? route('marketing-editor.update', $step->id) : null,
    'previewRoute' => isset($step) ? '/preview/marketing/' . $step->order : null,
    'editorHeight' => 800
])