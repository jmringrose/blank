@include('components.email-editor', [
    'type' => 'Question',
    'stepLabel' => 'Question',
    'orderLabel' => 'Question ID',
    'cancelRoute' => route('question-steps.index'),
    'storeRoute' => route('question-editor.store'),
    'updateRoute' => isset($step) ? route('question-editor.update', $step->id) : null,
    'editorHeight' => 600
])