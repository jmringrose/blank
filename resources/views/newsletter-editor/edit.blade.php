@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            {{ isset($step) ? 'Edit Newsletter' : 'Create Newsletter' }}
            @if(isset($step))
                <small class="text-sm text-gray-500 block">File: {{ $step->filename }}</small>
            @endif
        </h1>
        <a href="{{ route('newsletter-editor.index') }}" class="btn btn-outline">← Back to List</a>
    </div>

    <div class="bg-base-100 rounded-lg shadow p-6">
        <form method="POST" action="{{ isset($step) ? route('newsletter-editor.update', $step->id) : route('newsletter-editor.store') }}">
            @csrf
            @if(isset($step))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Title</span>
                    </label>
                    <input type="text" name="title" class="input input-bordered" 
                           value="{{ old('title', $step->title ?? '') }}" required>
                    @error('title')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if(!isset($step))
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Order</span>
                    </label>
                    <input type="number" name="order" class="input input-bordered" 
                           value="{{ old('order', 1) }}" required min="1">
                    @error('order')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>
                @endif
            </div>

            <div class="form-control mb-6">
                <label class="label">
                    <span class="label-text">Content</span>
                </label>
                <div id="editor" style="height: 400px;"></div>
                <textarea name="content" id="content" style="display: none;" required>{{ old('content', $currentContent ?? '') }}</textarea>
                @error('content')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror

            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('newsletter-editor.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ isset($step) ? 'Update' : 'Create' }} Newsletter</button>
            </div>
        </form>
    </div>
</div>

@push('head')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
.ql-editor {
    background-color: white !important;
    color: black !important;
}
.ql-toolbar {
    background-color: #8B7355 !important;
    color: #FDE047 !important;
    border-bottom: 1px solid #6B5B47 !important;
}
.ql-toolbar .ql-stroke {
    stroke: #FDE047 !important;
}
.ql-toolbar .ql-fill {
    fill: #FDE047 !important;
}
.ql-toolbar button:hover {
    background-color: #6B5B47 !important;
}
</style>
@endpush

@push('scripts')
<script>
window.addEventListener('load', function() {
    if (typeof Quill !== 'undefined') {
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['link', 'image'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        // Set initial content
        var content = document.getElementById('content').value;
        
        if (content && content.trim()) {
            quill.root.innerHTML = content;
        }

        // Update hidden textarea on form submit
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('content').value = quill.root.innerHTML;
        });
    }
});
</script>
@endpush
@endsection