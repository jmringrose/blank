@push('head')
    <script src="https://unpkg.com/tinymce@5/tinymce.min.js"></script>
@endpush

@push('scripts')
    <script>
        const emailEditorConfig = {
            height: {{ $height ?? 500 }},
            // Turn the menubar back on and organize items
            menubar: 'edit insert format table view tools help',

            // Keep browser spellcheck
            browser_spellcheck: true,
            contextmenu: false,

            // Root block and language
            forced_root_block: 'p',
            forced_root_block_attrs: { lang: 'en-US' },

            // Keep only the plugins you actually use
            plugins: [
                'advlist autolink lists link image charmap preview',
                'searchreplace anchor code fullscreen',
                'insertdatetime media table help wordcount'
            ].join(' '),

            // Minimal toolbar to reduce icon clutter
            // Add or remove to taste
            toolbar: 'styleselect | bold italic underline | bullist numlist | alignleft aligncenter alignright | link image | spellchecktoggle | undo redo',

            // Logical pulldowns
            menu: {
                edit:  { title: 'Edit',  items: 'undo redo | cut copy paste | searchreplace removeformat' },
                insert:{ title: 'Insert',items: 'link image media charmap hr | anchor insertdatetime' },
                format:{ title: 'Format',items: 'styleselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright | superscript subscript' },
                table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
                view:  { title: 'View',  items: 'preview | code fullscreen' },
                tools: { title: 'Tools', items: 'wordcount' },
                help:  { title: 'Help',  items: 'help' }
            },

            // Your semantic formats
            formats: {
                bold: { inline: 'strong' },
                italic: { inline: 'em' },
                underline: { inline: 'span', styles: { textDecoration: 'underline' } },
                alignleft: { block: 'p', styles: { textAlign: 'left' } },
                aligncenter: { block: 'p', styles: { textAlign: 'center' } },
                alignright: { block: 'p', styles: { textAlign: 'right' } },
                h1: { block: 'h1', styles: { fontSize: '24px', fontWeight: 'bold', margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif' } },
                h2: { block: 'h2', styles: { fontSize: '20px', fontWeight: 'bold', margin: '0 0 14px 0', fontFamily: 'Arial, sans-serif' } },
                h3: { block: 'h3', styles: { fontSize: '18px', fontWeight: 'bold', margin: '0 0 12px 0', fontFamily: 'Arial, sans-serif' } },
                p:  { block: 'p',  styles: { margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif', fontSize: '16px', lineHeight: '1.6' } }
            },

            style_formats: [
                { title: 'Paragraph', format: 'p' },
                { title: 'Heading 1', format: 'h1' },
                { title: 'Heading 2', format: 'h2' },
                { title: 'Heading 3', format: 'h3' },
                { title: 'Heading 4', format: 'h4' },
                { title: 'Red Text',  inline: 'span', styles: { color: '#cc0000' } },
                { title: 'Blue Text', inline: 'span', styles: { color: '#0066cc' } },
                { title: 'Large Text', inline: 'span', styles: { fontSize: '18px' } },
                { title: 'Small Text', inline: 'span', styles: { fontSize: '12px' } },
                { title: 'Call Out', block: 'div', styles: { backgroundColor: '#f0f8ff', border: '2px solid #007cba', padding: '15px', margin: '10px 0', borderRadius: '5px' } },
                { title: 'Badge', inline: 'span', styles: { backgroundColor: '#007cba', color: '#ffffff', padding: '4px 8px', borderRadius: '12px', fontSize: '12px', fontWeight: 'bold' } },
                { title: 'Footer Box', block: 'div', styles: { backgroundColor: '#0678b4', color: '#ffffff', padding: '4px 8px', borderRadius: '8px', fontSize: '12px', fontWeight: 'bold', width: '600px', height: '100px', border: '2px solid', borderColor: '#ffa922' } }
            ],

            // Editor preview CSS to mimic your email
            content_style: `
    body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 0; padding: 20px; background: #eff1f3; }
    p { margin: 0 0 16px 0; }
    h1, h2, h3 { margin: 0 0 16px 0; }
    table { border-collapse: collapse; }
    .email-container { width: 100% !important; max-width: 610px !important; margin: 0 auto !important; }
    @media only screen and (min-width: 601px) {
      .email-container { width: 610px !important; margin-left: auto !important; margin-right: auto !important; }
    }
    img { max-width: 100%; height: auto; display: block; }
  `,

            style_formats_merge: false,
            valid_elements: '*[*]',
            extended_valid_elements: 'table[role|cellpadding|cellspacing|border|width|style|class],td[align|valign|width|style|class],tr[style|class],*[*]',
            valid_styles: false,
            custom_elements: '~v:roundrect,~v:*,~w:*',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            document_base_url: '',
            verify_html: false,
            cleanup: false,
            fix_list_elements: false,
            keep_styles: true,
            remove_trailing_brs: false,
            entity_encoding: 'raw',
            allow_unsafe_link_target: true,
            table_resize_bars: false,
            table_grid: false,
            object_resizing: false
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
                    .then(r => r.json())
                    .then(result => success(result.url))
                    .catch(err => failure('Image upload failed: ' + err.message));
            },

            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') showImageBrowser(callback);
            },

            setup: function(editor) {
                // spellcheck + language inside the iframe
                editor.on('init', () => {
                    const doc = editor.getDoc();
                    doc.documentElement.setAttribute('lang', 'en-US');
                    editor.getBody().setAttribute('lang', 'en-US');
                    editor.getBody().setAttribute('spellcheck', 'true');
                    editor.getBody().spellcheck = true;
                });

                // custom toggle remains
                let isOn = true;
                editor.ui.registry.addToggleButton('spellchecktoggle', {
                    text: 'Spelling',
                    tooltip: 'Toggle browser spellcheck',
                    onAction: () => {
                        isOn = !isOn;
                        editor.getBody().setAttribute('spellcheck', isOn ? 'true' : 'false');
                        editor.notificationManager.open({
                            text: `Spellcheck ${isOn ? 'enabled' : 'disabled'}`,
                            type: 'info',
                            timeout: 1500
                        });
                    },
                    onSetup: (api) => { api.setActive(isOn); return () => {}; }
                });

                // auto-apply email image styles
                editor.on('NodeChange', () => {
                    const imgs = editor.getBody().querySelectorAll('img:not([data-email-styled])');
                    imgs.forEach(img => {
                        const w = img.getAttribute('width');
                        const style = img.getAttribute('style') || '';
                        if (w === '600' && !style.includes('width:')) {
                            img.setAttribute('style', 'width:100%; max-width:600px; height:auto; display:block; border:0;');
                        }
                        img.setAttribute('data-email-styled', 'true');
                    });
                });
            }
        });
function showImageBrowser(callback) {
    const modalHTML = '<div id="imageBrowserModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center" style="z-index: 99999;">' +
        '<div class="bg-stone-200 rounded-lg p-6 max-w-4xl w-4/5 max-h-4/5 overflow-y-auto">' +
        '<div class="flex justify-between items-center"><h3 class="text-lg font-bold dark:text-stone-800 text-gray-200">Select an Image</h3>' +
        '<button onclick="closeImageBrowser()" class="bg-blue-500 hover:bg-gray-900 border-none rounded px-3 py-2 cursor-pointer">✕</button>' +
        '</div><div id="imageGrid" class="grid grid-cols-6 gap-3 mb-4 mt-2"><div class="col-span-6 text-center text-gray-500">Loading images...</div></div>' +
        '<div class="text-center"><button onclick="closeImageBrowser()" class="btn btn-info btn-sm">Cancel</button></div></div></div>';

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    window.imageCallbackFn = callback;

    fetch('/images')
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('imageGrid');
            const images = data.images || data || [];

            if (!Array.isArray(images) || images.length === 0) {
                grid.innerHTML = '<div class="col-span-6 text-center text-gray-500">No images found</div>';
                return;
            }

            grid.innerHTML = '';
            images.forEach(image => {
                const div = document.createElement('div');
                div.className = 'cursor-pointer border-2 hover:border-blue-400 rounded p-2 transition-all bg-gray-200 border border-gray-300  shadow-md';
                div.onclick = () => selectImage(image.url, image.name);

                const img = document.createElement('img');
                img.src = image.url;
                img.alt = image.name;
                img.className = 'w-full h-20 object-cover rounded mb-2';

                const nameDiv = document.createElement('div');
                nameDiv.className = 'text-xs text-center text-gray-700 overflow-hidden text-ellipsis whitespace-nowrap';
                nameDiv.title = image.name;
                nameDiv.textContent = image.name;

                div.appendChild(img);
                div.appendChild(nameDiv);
                grid.appendChild(div);
            });
        })
        .catch(error => {
            document.getElementById('imageGrid').innerHTML = '<div class="col-span-6 text-center text-red-500">Error loading images</div>';
        });
}

function selectImage(url, alt) {
    if (window.imageCallbackFn) {
        // Apply email-friendly styles automatically
        const emailStyles = {
            alt: alt || '',
            title: alt || '',
            width: '600',
            style: 'width:100%; max-width:600px; height:auto; display:block; border:0;'
        };
        window.imageCallbackFn(url, emailStyles);
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
