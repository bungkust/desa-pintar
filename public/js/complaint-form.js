// Complaint Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('complaint-form');
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');
    const anonymousCheckbox = document.querySelector('input[name="is_anonymous"]');
    const phoneInput = document.querySelector('input[name="phone"]');

    // Handle anonymous checkbox
    if (anonymousCheckbox) {
        anonymousCheckbox.addEventListener('change', function() {
            if (this.checked) {
                phoneInput.removeAttribute('required');
                phoneInput.closest('.mb-4').querySelector('label').querySelector('.text-red-500').style.display = 'none';
            } else {
                phoneInput.setAttribute('required', 'required');
                phoneInput.closest('.mb-4').querySelector('label').querySelector('.text-red-500').style.display = 'inline';
            }
        });
    }

    // Image preview
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = Array.from(e.target.files).slice(0, 3); // Max 3 images

            files.forEach((file, index) => {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    alert(`File ${file.name} terlalu besar. Maksimal 2MB per gambar.`);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}" 
                             class="w-full h-32 object-cover rounded-lg">
                        <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                                onclick="this.parentElement.remove(); updateFileInput();">
                            ×
                        </button>
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate phone if not anonymous
            if (!anonymousCheckbox.checked && !phoneInput.value.trim()) {
                e.preventDefault();
                alert('Nomor telepon wajib diisi jika tidak melaporkan sebagai anonim.');
                phoneInput.focus();
                return false;
            }

            // Validate images
            const files = imageInput?.files;
            if (files && files.length > 3) {
                e.preventDefault();
                alert('Maksimal 3 gambar.');
                return false;
            }

            // Check file sizes
            if (files) {
                for (let file of files) {
                    if (file.size > 2 * 1024 * 1024) {
                        e.preventDefault();
                        alert(`File ${file.name} terlalu besar. Maksimal 2MB per gambar.`);
                        return false;
                    }
                }
            }
        });
    }

    // Update file input when removing preview
    window.updateFileInput = function() {
        // This would require more complex logic to sync with actual file input
        // For now, just clear the preview
    };
});

