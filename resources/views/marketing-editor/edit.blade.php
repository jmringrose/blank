@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-base-100 rounded-lg shadow p-6 relative">
        <a href="{{ route('marketing-steps.index') }}" class="btn btn-outline absolute top-4 right-4">Cancel</a>
        
        <div class="mb-6">
            @if(isset($step))
                <h1 class="text-2xl font-bold mb-2">{{ $step->title }}</h1>
                <small class="text-sm text-gray-500">Step: {{ $step->order }}</small>
            @else
                <h1 class="text-2xl font-bold">Create Marketing Email</h1>
            @endif
        </div>
        <form method="POST" action="{{ isset($step) ? route('marketing-editor.update', $step->id) : route('marketing-editor.store') }}">
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
                    <span class="label-text">Step Number</span>
                </label>
                <input type="number" name="order" class="input input-bordered"
                       value="{{ old('order', 1) }}" required min="1" max="6">
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
                    <a href="{{ route('email.preview.marketing', $step->order) }}" class="btn btn-info" target="_blank">👁️ View</a>
                    <button type="submit" name="action" value="save_continue" class="btn btn-success">Save & Continue</button>
                    <button type="submit" name="action" value="save" class="btn btn-primary">Update Marketing Email</button>
                @else
                    <button type="submit" name="action" value="save" class="btn btn-primary">Create Marketing Email</button>
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
const emailEditorConfig = {
  height: 600,
  menubar: false,
  plugins: 'advlist autolink lists link image charmap preview anchor code fullscreen insertdatetime media table help wordcount',
  toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image tables | code',

  formats: {
    bold: {inline: 'strong'},
    italic: {inline: 'em'},
    underline: {inline: 'span', styles: {textDecoration: 'underline'}},
    alignleft: {block: 'p', styles: {textAlign: 'left'}},
    aligncenter: {block: 'p', styles: {textAlign: 'center'}},
    alignright: {block: 'p', styles: {textAlign: 'right'}},
    h1: {block: 'h1', styles: {fontSize: '24px', fontWeight: 'bold', margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif'}},
    h2: {block: 'h2', styles: {fontSize: '20px', fontWeight: 'bold', margin: '0 0 14px 0', fontFamily: 'Arial, sans-serif'}},
    h3: {block: 'h3', styles: {fontSize: '18px', fontWeight: 'bold', margin: '0 0 12px 0', fontFamily: 'Arial, sans-serif'}},
    p: {block: 'p', styles: {margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif', fontSize: '14px', lineHeight: '1.4'}}
  },

    style_formats: [
        {title: 'Paragraph', format: 'p'},
        {title: 'Heading 1', format: 'h1'},
        {title: 'Heading 2', format: 'h2'},
        {title: 'Heading 3', format: 'h3'},
        {title: 'Red Text', inline: 'span', styles: {color: '#cc0000'}},
        {title: 'Blue Text', inline: 'span', styles: {color: '#0066cc'}},
        {title: 'Large Text', inline: 'span', styles: {fontSize: '18px'}},
        {title: 'Small Text', inline: 'span', styles: {fontSize: '12px'}},
        {title: 'Call Out', block: 'div', styles: {backgroundColor: '#f0f8ff', border: '2px solid #007cba', padding: '15px', margin: '10px 0', borderRadius: '5px'}},
        {title: ' Badge ', inline: 'span', styles: {backgroundColor: '#007cba', color: '#ffffff', padding: '4px 8px', borderRadius: '12px', fontSize: '12px', fontWeight: 'bold'}},
        {title: ' Footer Box ', block: 'div', styles: { backgroundColor: '#0678b4', color: '#ffffff', padding: '4px 8px', borderRadius: '8px', fontSize: '12px', fontWeight: 'bold', width: '600px', height: '100px', border: '2px solid', 'border-color': '#ffa922'
            }}
    ],
  content_style: `
    body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 0; padding: 20px; }
    p { margin: 0 0 16px 0; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; }
    h1, h2, h3 { margin: 0 0 16px 0; font-family: Arial, sans-serif; }
  `,

  style_formats_merge: false,
  forced_root_block: 'p',
  valid_elements: '*[style|src|alt|title|width|height|href|target]',
  extended_valid_elements: 'img[src|alt|title|width|height|style],a[href|target|style],*[style]'
};

tinymce.init({
    selector: '#content',
    ...emailEditorConfig,

    // Marketing-specific overrides
    height: 800,

    // Image handling configuration
    automatic_uploads: false,
    images_upload_credentials: false,

    // Configure relative URLs
    relative_urls: false,
    remove_script_host: false,
    convert_urls: false,

    // Preserve all attributes
    valid_children: '+body[style],+body[img]',

    // Image plugin configuration
    image_title: true,
    image_description: false,
    image_dimensions: true,
    image_class_list: [
        { title: 'Responsive', value: 'img-responsive' },
        { title: 'Centered', value: 'mx-auto block' }
    ],

    // Image upload handler
    images_upload_handler: function(blobInfo, success, failure) {
        const formData = new FormData();
        formData.append('image', blobInfo.blob(), blobInfo.filename());

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/upload-image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            success(result.url);
        })
        .catch(error => {
            failure('Image upload failed: ' + error.message);
        });
    },

    // File picker for browsing existing images
    file_picker_callback: function(callback, value, meta) {
        if (meta.filetype === 'image') {
            showImageBrowser(callback);
        }
    },

    // Preserve Blade variables
    protect: [
        /\{\{.*?\}\}/g
    ],

    // Don't encode entities for our variables
    entity_encoding: 'named',

    setup: function(editor) {
        editor.on('BeforeSetContent', function(e) {
            // First clean up any existing blade-var spans to prevent accumulation
            e.content = e.content.replace(/<span class="blade-var">(\{\{[^}]+\}\})<\/span>/g, '$1');
            // Then protect Blade variables during content setting
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

// Custom Image Browser Function
function showImageBrowser(callback) {
    const modalHTML = `
        <div id="imageBrowserModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 99999;">
            <div style="background: white; border-radius: 8px; padding: 24px; max-width: 80vw; width: 800px; max-height: 80vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: bold; color: #1f2937; margin: 0;">Select an Image</h3>
                    <button onclick="closeImageBrowser()" style="background: #f3f4f6; border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer; color: #374151;">✕</button>
                </div>
                <div id="imageGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-bottom: 16px;">
                    <div style="text-align: center; color: #6b7280;">Loading images...</div>
                </div>
                <div style="text-align: center;">
                    <button onclick="closeImageBrowser()" style="background: #e5e7eb; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 16px; cursor: pointer; color: #374151;">Cancel</button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    window.imageCallbackFn = callback;

    fetch('/images')
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('imageGrid');
            const images = data.images || data || [];

            if (!Array.isArray(images) || images.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #6b7280;">No images found in /public/img/newsletters folder</div>';
                return;
            }

            grid.innerHTML = images.map(image => `
                <div onclick="selectImage('${image.url}', '${image.name}')"
                     style="cursor: pointer; border: 2px solid transparent; border-radius: 4px; padding: 8px; transition: all 0.2s; background: #f9fafb;"
                     onmouseover="this.style.borderColor='#3b82f6'; this.style.background='#eff6ff';"
                     onmouseout="this.style.borderColor='transparent'; this.style.background='#f9fafb';">
                    <img src="${image.url}" alt="${image.name}"
                         style="width: 100%; height: 80px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;">
                    <div style="font-size: 12px; text-align: center; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${image.name}">${image.name}</div>
                </div>
            `).join('');
        })
        .catch(error => {
            const grid = document.getElementById('imageGrid');
            if (grid) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444;">Error loading images: ' + error.message + '</div>';
            }
        });
}

function selectImage(url, alt) {
    if (window.imageCallbackFn) {
        window.imageCallbackFn(url, {
            alt: alt || '',
            title: alt || ''
        });
        closeImageBrowser();
    }
}

function closeImageBrowser() {
    const modal = document.getElementById('imageBrowserModal');
    if (modal) {
        modal.remove();
    }
    window.imageCallbackFn = null;
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    var content = tinymce.get('content').getContent();
    if (!content || content.trim() === '') {
        e.preventDefault();
        alert('Please enter some content for the email.');
        return false;
    }
});
</script>
@endpush
@endsection
