w/**
 * TinyMCE Image Browser for Laravel Application
 * Provides a custom file picker for selecting images from /public/img folder
 */

// Global variables
window.imageCallbackFn = null;

/**
 * Show the image browser modal
 * @param {Function} callback - TinyMCE callback function
 */
function showImageBrowser(callback) {
    // Create modal HTML
    const modalHTML = `
        <div id="imageBrowserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-96 overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Select an Image</h3>
                    <button onclick="closeImageBrowser()" class="btn btn-sm btn-ghost">✕</button>
                </div>
                <div id="imageGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div class="text-center">Loading images...</div>
                </div>
                <div class="mt-4 text-center">
                    <button onclick="uploadNewImage()" class="btn btn-secondary mr-2">Upload New</button>
                    <button onclick="closeImageBrowser()" class="btn btn-outline">Cancel</button>
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Store callback for later use
    window.imageCallbackFn = callback;

    // Load images from server
    loadImages();
}

/**
 * Load images from the server
 */
function loadImages() {
    fetch('/api/images')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(images => {
            const grid = document.getElementById('imageGrid');
            if (!grid) return;

            if (images.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center text-gray-500">No images found in /public/img folder</div>';
                return;
            }

            grid.innerHTML = images.map(image => `
                <div class="cursor-pointer hover:opacity-75 border-2 border-transparent hover:border-blue-500 rounded p-2 transition-all duration-200"
                     onclick="selectImage('${image.url}', '${image.name}')">
                    <img src="${image.url}" alt="${image.name}" class="w-full h-20 object-cover rounded mb-2" loading="lazy">
                    <div class="text-xs text-center truncate" title="${image.name}">${image.name}</div>
                    <div class="text-xs text-center text-gray-500">${formatFileSize(image.size)}</div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading images:', error);
            const grid = document.getElementById('imageGrid');
            if (grid) {
                grid.innerHTML = '<div class="col-span-full text-center text-red-500">Error loading images. Please try again.</div>';
            }
        });
}

/**
 * Select image and close modal
 * @param {string} url - Image URL
 * @param {string} alt - Alt text
 */
function selectImage(url, alt) {
    if (window.imageCallbackFn) {
        window.imageCallbackFn(url, { alt: alt });
        closeImageBrowser();
    }
}

/**
 * Upload new image (fallback to file picker)
 */
function uploadNewImage() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.onchange = function() {
        const file = this.files[0];
        if (file) {
            // For now, just use the filename approach
            // In a real implementation, you'd upload to server
            const imageName = prompt('Enter filename for the image (it should exist in /img folder):', file.name);
            if (imageName) {
                const imageUrl = '/img/' + imageName;
                selectImage(imageUrl, imageName);
            }
        }
    };

    input.click();
}

/**
 * Close image browser modal
 */
function closeImageBrowser() {
    const modal = document.getElementById('imageBrowserModal');
    if (modal) {
        modal.remove();
    }
    window.imageCallbackFn = null;
}

/**
 * Format file size for display
 * @param {number} bytes - File size in bytes
 * @returns {string} Formatted size
 */
function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Export for use in modules if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showImageBrowser,
        selectImage,
        closeImageBrowser
    };
}
