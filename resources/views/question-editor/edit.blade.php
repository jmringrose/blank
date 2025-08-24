@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-base-100 rounded-lg shadow p-6 relative">
        <a href="{{ route('question-steps.index') }}" class="btn btn-outline absolute top-4 right-4">Cancel</a>
        
        <div class="mb-6">
            @if(isset($step))
                <h1 class="text-2xl font-bold mb-2">{{ $step->title }}</h1>
                <small class="text-sm text-gray-500">Question: {{ $step->order }}</small>
            @else
                <h1 class="text-2xl font-bold">Create Question Email</h1>
            @endif
        </div>
        <form method="POST" action="{{ isset($step) ? route('question-editor.update', $step->id) : route('question-editor.store') }}">
            @csrf
            @if(isset($step))
                @method('PUT')
            @endif

            @if(!isset($step))
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text mr-2">Title: </span>
                </label>
                <input type="text" name="title" class="input input-bordered"
                       value="{{ old('title', $step->title ?? '') }}" required>
                @error('title')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-control mb-6">
                <label class="label">
                    <span class="label-text">Question ID</span>
                </label>
                <input type="number" name="order" class="input input-bordered"
                       value="{{ old('order', 1) }}" required>
                @error('order')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>
            @else
            <input type="hidden" name="title" value="{{ $step->title }}">
            @endif

            <div class="form-control mb-6" v-pre>
                <textarea name="content" id="content">{{ old('content', $currentContent ?? '') }}</textarea>
                @error('content')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-1">
                @if(isset($step))
                    <button type="submit" name="action" value="save_continue" class="btn btn-success">Save & Continue</button>
                    <button type="submit" name="action" value="save" class="btn btn-primary">Update Question Email</button>
                @else
                    <button type="submit" name="action" value="save" class="btn btn-primary">Create Question Email</button>
                @endif
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
    height: 600,
    menubar: false,
    plugins: 'advlist autolink lists link image charmap preview anchor code fullscreen insertdatetime media table help wordcount',
    toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image tables | code',
    
    content_style: `
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 0; padding: 20px; }
        p { margin: 0 0 16px 0; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; }
        h1, h2, h3 { margin: 0 0 16px 0; font-family: Arial, sans-serif; }
    `,
    
    images_upload_handler: function(blobInfo, success, failure) {
        const formData = new FormData();
        formData.append('image', blobInfo.blob(), blobInfo.filename());
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/upload-image', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken || '' },
            body: formData
        })
        .then(response => response.json())
        .then(result => success(result.url))
        .catch(error => failure('Image upload failed: ' + error.message));
    }
});
</script>
@endpush
@endsection