// Select All checkbox functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Add color coding to select dropdowns based on selection
        document.querySelectorAll('.promotion-select').forEach(select => {
            select.addEventListener('change', function() {
                // Remove all status classes
                this.classList.remove('promote', 'graduate', 'retain', 'pending');
                
                // Add appropriate class
                const value = this.value.toLowerCase().replace(' to ', '-').replace(' ', '-');
                if (value.includes('promote')) {
                    this.classList.add('promote');
                } else if (value.includes('graduate')) {
                    this.classList.add('graduate');
                } else if (value.includes('retain')) {
                    this.classList.add('retain');
                } else {
                    this.classList.add('pending');
                }
            });
            
            // Initialize class on load
            select.dispatchEvent(new Event('change'));
        });
        
        // ==============================
        // SUCCESS MODAL FUNCTIONS
        // ==============================
        function showSuccessModal(message) {
            const successModal = document.getElementById("successModal");
            const successMessage = document.getElementById("successMessage");
            
            if (successMessage) {
                successMessage.textContent = message;
            }
            
            if (successModal) {
                successModal.classList.add("active");
            }
        }

        function closeSuccessModal(shouldReload = false) {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.classList.remove('active');
            }
            // Only reload if explicitly requested
            if (shouldReload === true) {
                location.reload();
            }
        }
        
        // Make function globally accessible
        window.closeSuccessModal = closeSuccessModal;
        
        // Close modal when clicking outside
        window.addEventListener("click", function(event) {
            const successModal = document.getElementById("successModal");
            if (successModal && event.target === successModal) {
                closeSuccessModal();
            }
        });
        
        // Handle form submission with loading modal
        const promotionForm = document.getElementById('promotionForm');
        if (promotionForm) {
            promotionForm.addEventListener('submit', function(e) {
                // Check if submitting Save All or Bulk Update
                const submitBtn = e.submitter;
                const isBulkUpdate = submitBtn && submitBtn.name === 'bulk_update_promotion';
                
                if (isBulkUpdate) {
                    // For bulk update, check if students are selected
                    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
                    if (selectedCheckboxes.length === 0) {
                        alert('Please select at least one student.');
                        e.preventDefault();
                        return;
                    }
                    
                    const bulkStatus = document.getElementById('bulkStatus').value;
                    if (bulkStatus === 'Pending') {
                        alert('Please select a valid status (not Pending).');
                        e.preventDefault();
                        return;
                    }
                    
                    // Show loading modal
                    const loadingModal = document.getElementById('loadingModal');
                    if (loadingModal) {
                        loadingModal.classList.add('active');
                    }
                }
                // For Save All, let the form submit normally (no AJAX)
            });
        }