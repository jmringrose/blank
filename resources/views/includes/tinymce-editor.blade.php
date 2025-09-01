@push('head')
<script src="https://unpkg.com/tinymce@5/tinymce.min.js"></script>
@endpush

@push('scripts')
<script>
const emailEditorConfig = {
  height: {{ $height ?? 500 }},
  menubar: true,
  plugins: 'advlist autolink lists link image charmap preview anchor code fullscreen insertdatetime media table help wordcount',
  toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link image | table | code',

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
    h4: {block: 'h4', styles: {fontSize: '16px', fontWeight: 'bold', margin: '0 0 12px 0', fontFamily: 'Arial, sans-serif'}},
    p: {block: 'p', styles: {margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif', fontSize: '16px', lineHeight: '1.6'}}
  },

  content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 0; padding: 20px; } p { margin: 0 0 16px 0; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; } h1, h2, h3 { margin: 0 0 16px 0; font-family: Arial, sans-serif; }',

    // Custom style formats for emails
    style_formats: [
        {title: 'Paragraph', format: 'p'},
        {title: 'Heading 1', format: 'h1'},
        {title: 'Heading 2', format: 'h2'},
        {title: 'Heading 3', format: 'h3'},
        {title: 'Heading 4', format: 'h4'},
        {title: 'Red Text', inline: 'span', styles: {color: '#cc0000'}},
        {title: 'Blue Text', inline: 'span', styles: {color: '#0066cc'}},
        {title: 'Large Text', inline: 'span', styles: {fontSize: '18px'}},
        {title: 'Small Text', inline: 'span', styles: {fontSize: '12px'}},
        {title: 'Call Out', block: 'div', styles: {backgroundColor: '#f0f8ff', border: '2px solid #007cba', padding: '15px', margin: '10px 0', borderRadius: '5px'}},
        {title: ' Badge ', inline: 'span', styles: {backgroundColor: '#007cba', color: '#ffffff', padding: '4px 8px', borderRadius: '12px', fontSize: '12px', fontWeight: 'bold'}},
        {title: ' Footer Box ', block: 'div', styles: { backgroundColor: '#0678b4', color: '#ffffff', padding: '4px 8px', borderRadius: '8px', fontSize: '12px', fontWeight: 'bold', width: '600px', height: '100px', border: '2px solid', 'border-color': '#ffa922'
            }}
    ],

  forced_root_block: 'p',
  valid_elements: '*[style|src|alt|title|width|height|href|target|colspan],#text',
  extended_valid_elements: 'img[src|alt|title|width|height|style],a[href|target|style],*[style]table,tr,td[colspan|rowspan],th[colspan|rowspan]',
  relative_urls: false,
  remove_script_host: false,
  convert_urls: false,
  verify_html: false,
  cleanup: false

};

tinymce.init({
    selector: '#content',
    ...emailEditorConfig,
    

    


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
    },

    file_picker_callback: function(callback, value, meta) {
        if (meta.filetype === 'image') {
            showImageBrowser(callback);
        }
    }
});

function showImageBrowser(callback) {
    const modalHTML = '<div id="imageBrowserModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 99999;"><div style="background: white; border-radius: 8px; padding: 24px; max-width: 80vw; width: 800px; max-height: 80vh; overflow-y: auto;"><div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;"><h3 style="font-size: 18px; font-weight: bold; margin: 0;">Select an Image</h3><button onclick="closeImageBrowser()" style="background: #f3f4f6; border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer;">✕</button></div><div id="imageGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-bottom: 16px;"><div style="text-align: center; color: #6b7280;">Loading images...</div></div><div style="text-align: center;"><button onclick="closeImageBrowser()" style="background: #e5e7eb; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px 16px; cursor: pointer;">Cancel</button></div></div></div>';

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    window.imageCallbackFn = callback;

    fetch('/images')
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('imageGrid');
            const images = data.images || data || [];

            if (!Array.isArray(images) || images.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #6b7280;">No images found</div>';
                return;
            }

            grid.innerHTML = '';
            images.forEach(image => {
                const div = document.createElement('div');
                div.style.cssText = 'cursor: pointer; border: 2px solid transparent; border-radius: 4px; padding: 8px; transition: all 0.2s; background: #f9fafb;';
                div.onmouseover = () => div.style.borderColor = '#3b82f6';
                div.onmouseout = () => div.style.borderColor = 'transparent';
                div.onclick = () => selectImage(image.url, image.name);

                const img = document.createElement('img');
                img.src = image.url;
                img.alt = image.name;
                img.style.cssText = 'width: 100%; height: 80px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;';

                const nameDiv = document.createElement('div');
                nameDiv.style.cssText = 'font-size: 12px; text-align: center; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                nameDiv.title = image.name;
                nameDiv.textContent = image.name;

                div.appendChild(img);
                div.appendChild(nameDiv);
                grid.appendChild(div);
            });
        })
        .catch(error => {
            document.getElementById('imageGrid').innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444;">Error loading images</div>';
        });
}

function selectImage(url, alt) {
    if (window.imageCallbackFn) {
        window.imageCallbackFn(url, { alt: alt || '', title: alt || '' });
        closeImageBrowser();
    }
}

function closeImageBrowser() {
    const modal = document.getElementById('imageBrowserModal');
    if (modal) modal.remove();
    window.imageCallbackFn = null;
}
</script>
@endpush
