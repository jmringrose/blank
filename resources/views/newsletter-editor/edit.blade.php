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
        <a href="{{ route('newsletter-steps.index') }}" class="btn btn-outline">← Back to Newsletter Steps</a>
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

            <div class="form-control mb-6" v-pre>
                <label class="label">
                    <span class="label-text">Content</span>
                </label>
                <textarea name="content" id="content">{{ old('content', $currentContent ?? '') }}</textarea>
                @error('content')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('newsletter-steps.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ isset($step) ? 'Update' : 'Create' }} Newsletter</button>
            </div>
        </form>
    </div>
</div>

@push('head')
<script src="https://unpkg.com/tinymce@5/tinymce.min.js"></script>
@endpush

@push('scripts')
<script>
tinymce.init({
    selector: '#content',
    height: 400,
    plugins: 'lists link image code table',
    toolbar: 'undo redo | formatselect styleselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
    
    // Enable extended valid elements for styling
    extended_valid_elements: 'span[style|class],div[style|class],p[style|class]',
    
    // Style formats dropdown
    style_formats: [
        {title: 'Text Colors', items: [
            {title: 'Red Text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Blue Text', inline: 'span', styles: {color: '#0066cc'}},
            {title: 'Green Text', inline: 'span', styles: {color: '#008000'}}
        ]},
        {title: 'Text Sizes', items: [
            {title: 'Large Text', inline: 'span', styles: {'font-size': '18px'}},
            {title: 'Small Text', inline: 'span', styles: {'font-size': '12px'}}
        ]},
        {title: 'Backgrounds', items: [
            {title: 'Yellow Highlight', inline: 'span', styles: {'background-color': '#ffff00'}},
            {title: 'Gray Background', block: 'div', styles: {'background-color': '#f5f5f5', 'padding': '10px'}}
        ]}
    ],
    menubar: false,
    branding: false,
    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
    
    // Image options
    image_title: true,
    image_description: false,
    image_dimensions: false,
    image_class_list: [
        {title: 'Responsive', value: 'img-responsive'},
        {title: 'Rounded', value: 'rounded'},
        {title: 'Shadow', value: 'shadow'}
    ],
    
    // File picker for images
    file_picker_callback: function(callback, value, meta) {
        if (meta.filetype === 'image') {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function() {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function() {
                    callback(reader.result, {
                        alt: file.name
                    });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    },
    
    // Enable custom style formats
    style_formats_merge: true,
    
    // Preserve Blade variables
    protect: [
        /\{\{.*?\}\}/g
    ],
    
    // Don't encode entities for our variables
    entity_encoding: 'named',
    
    setup: function(editor) {
        editor.on('BeforeSetContent', function(e) {
            // Protect Blade variables during content setting
            e.content = e.content.replace(/\{\{([^}]+)\}\}/g, function(match, variable) {
                return '<span class="blade-var">' + match + '</span>';
            });
        });
        
        editor.on('GetContent', function(e) {
            // Restore Blade variables when getting content
            e.content = e.content.replace(/<span class="blade-var">(\{\{[^}]+\}\})<\/span>/g, function(match, variable) {
                // Create a temporary element to decode HTML entities
                var temp = document.createElement('div');
                temp.innerHTML = variable;
                return temp.textContent || temp.innerText;
            });
        });
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    var content = tinymce.get('content').getContent();
    if (!content || content.trim() === '') {
        e.preventDefault();
        alert('Please enter some content for the newsletter.');
        return false;
    }
});
</script>
@endpush
@endsection