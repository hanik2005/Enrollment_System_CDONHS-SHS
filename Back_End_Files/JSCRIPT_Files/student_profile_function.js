let isEditing = false;

    function toggleEdit() {
        isEditing = true;
        document.getElementById('profileContent').classList.remove('view-mode');
        document.getElementById('profileContent').classList.add('edit-mode');
        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('saveBtn').style.display = 'inline-block';
        document.getElementById('cancelBtn').style.display = 'inline-block';
        
        // Enable all inputs except disabled ones
        const inputs = document.querySelectorAll('#profileContent input, #profileContent select');
        inputs.forEach(input => {
            if (!input.hasAttribute('disabled')) {
                input.removeAttribute('readonly');
            }
        });
    }

    function cancelEdit() {
        isEditing = false;
        document.getElementById('profileContent').classList.add('view-mode');
        document.getElementById('profileContent').classList.remove('edit-mode');
        document.getElementById('editBtn').style.display = 'inline-block';
        document.getElementById('saveBtn').style.display = 'none';
        document.getElementById('cancelBtn').style.display = 'none';
        
        // Reset form
        document.getElementById('profileForm').reset();
        
        // Disable inputs
        const inputs = document.querySelectorAll('#profileContent input, #profileContent select');
        inputs.forEach(input => {
            if (!input.hasAttribute('disabled')) {
                input.setAttribute('readonly', true);
            }
        });
    }

    // Initialize view mode
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('#profileContent input, #profileContent select');
        inputs.forEach(input => {
            if (!input.hasAttribute('disabled')) {
                input.setAttribute('readonly', true);
            }
        });
    });

    // Preview profile image before upload
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImagePreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
            
            // Auto-enable save button when profile image is selected
            if (!isEditing) {
                toggleEdit();
            }
        }
    }