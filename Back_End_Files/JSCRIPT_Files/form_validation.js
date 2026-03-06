// Handle dropdown with "Other" option
    function handleDropdownWithOther(selectElement, inputId) {
        const inputField = document.getElementById(inputId);
        if (selectElement.value === 'Other') {
            inputField.style.display = 'block';
            inputField.required = true;
            inputField.focus();
        } else if (selectElement.value !== '') {
            inputField.style.display = 'none';
            inputField.required = false;
            inputField.value = selectElement.value;
        } else {
            inputField.style.display = 'none';
            inputField.required = false;
            inputField.value = '';
        }
    }

    // Toggle functions for conditional fields
    function toggleIpSpecify() {
        var select = document.getElementById('indigenousCommunity');
        var div = document.getElementById('ipSpecifyDiv');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function toggleFourPsId() {
        var select = document.getElementById('fourPsBeneficiary');
        var div = document.getElementById('fourPsIdDiv');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function toggleDisabilityFields() {
        var select = document.getElementById('withDisability');
        var typeDiv = document.getElementById('disabilityTypeDiv');
        var manifestationDiv = document.getElementById('manifestationDiv');
        var pwdIdDiv = document.getElementById('pwdIdDiv');
        var pwdIdNumberDiv = document.getElementById('pwdIdNumberDiv');
        
        if (select.value === 'Yes') {
            typeDiv.style.display = 'block';
            manifestationDiv.style.display = 'block';
            pwdIdDiv.style.display = 'block';
            togglePwdIdNumber();
        } else {
            typeDiv.style.display = 'none';
            manifestationDiv.style.display = 'none';
            pwdIdDiv.style.display = 'none';
            pwdIdNumberDiv.style.display = 'none';
        }
    }

    function togglePwdIdNumber() {
        var select = document.getElementById('pwdId');
        var div = document.getElementById('pwdIdNumberDiv');
        if (select) {
            div.style.display = select.value === 'Yes' ? 'block' : 'none';
        }
    }

    function toggleGuardianFields() {
        var select = document.getElementById('hasGuardian');
        var div = document.getElementById('guardianFields');
        div.style.display = select.value === 'Yes' ? 'block' : 'none';
    }

    function togglePermanentAddress() {
        var select = document.getElementById('sameAsCurrent');
        var div = document.getElementById('permanentAddressDiv');
        div.style.display = select.value === 'No' ? 'block' : 'none';
    }

    function toggleLearningProgram() {
        var select = document.getElementById('attendedLearningProgram');
        var group = document.getElementById('learningProgramSpecifyGroup');
        if (select && group) {
            group.style.display = select.value === 'Yes' ? 'block' : 'none';
        }
    }

    // Real-time validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('enrollmentForm');
        const inputs = form.querySelectorAll('input, select');

        // Validation patterns and messages
        const validationRules = {
            required: {
                validate: (value) => value.trim() !== '',
                message: 'This field is required'
            },
            email: {
                validate: (value) => {
                    if (!value) return true;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(value);
                },
                message: 'Please enter a valid email address'
            },
            phone: {
                validate: (value) => {
                    if (!value) return true;
                    const phoneRegex = /^09[0-9]{9}$/;
                    return phoneRegex.test(value);
                },
                message: 'Please enter a valid 11-digit mobile number (e.g., 09123456789)'
            },
            lrn: {
                validate: (value) => {
                    if (!value) return true;
                    const lrnRegex = /^[0-9]{12}$/;
                    return lrnRegex.test(value);
                },
                message: 'LRN must be exactly 12 digits'
            },
            alpha: {
                validate: (value) => {
                    if (!value) return true;
                    const alphaRegex = /^[a-zA-Z\s\-'.ñÑ]+$/;
                    return alphaRegex.test(value);
                },
                message: 'Please enter only letters, spaces, hyphens, and apostrophes'
            },
            // New capitalization validation - checks if first letter of each word is uppercase
            capitalization: {
                validate: (value) => {
                    if (!value) return true;
                    // Check if first letter of each word is capitalized
                    const words = value.trim().split(/\s+/);
                    for (const word of words) {
                        if (word.length > 0 && word[0] !== word[0].toUpperCase()) {
                            return false;
                        }
                    }
                    return true;
                },
                message: 'Please use proper capitalization (e.g., Juan Dela Cruz)'
            },
            extension: {
                validate: (value) => {
                    if (!value) return true;
                    const extRegex = /^(Jr\.?|Sr\.?|II|III|IV|V)$/i;
                    return extRegex.test(value);
                },
                message: 'Valid extensions: Jr., Sr., II, III, IV, V'
            },
            date: {
                validate: (value) => {
                    if (!value) return true;
                    const date = new Date(value);
                    const now = new Date();
                    const minDate = new Date();
                    minDate.setFullYear(minDate.getFullYear() - 25);
                    return date <= now && date >= minDate;
                },
                message: 'Please enter a valid date (within the last 25 years)'
            },
            zip: {
                validate: (value) => {
                    if (!value) return true;
                    const zipRegex = /^[0-9]{4}$/;
                    return zipRegex.test(value);
                },
                message: 'Please enter a valid 4-digit zip code'
            },
            url: {
                validate: (value) => {
                    if (!value) return true;
                    try {
                        new URL(value);
                        return true;
                    } catch {
                        return false;
                    }
                },
                message: 'Please enter a valid URL (e.g., https://facebook.com/username)'
            },
            select: {
                validate: (value) => {
                    // For select elements, empty string means not selected
                    return value !== '';
                },
                message: 'Please select an option'
            }
        };

        // Auto-capitalize function for name fields
        function capitalizeFirstLetter(str) {
            if (!str) return '';
            return str.replace(/\b\w/g, char => char.toUpperCase());
        }

        // Add auto-capitalize to text inputs on blur
        const textInputs = form.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            // Skip LRN (should remain numeric)
            if (input.name === 'lrn') {
                return;
            }
            
            // Apply capitalization on blur
            input.addEventListener('blur', function() {
                if (this.value) {
                    this.value = capitalizeFirstLetter(this.value.trim());
                }
            });
            
            // Also apply on change for dropdown "Other" inputs
            input.addEventListener('change', function() {
                if (this.value) {
                    this.value = capitalizeFirstLetter(this.value.trim());
                }
            });
        });

        // Also capitalize dropdown select values when changed
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // If user selects "Other", the input field will be handled by the text input listener above
            });
        });

        // Validate single field
        function validateField(input) {
            const formGroup = input.closest('.form-group');
            if (!formGroup) return true;

            const errorSpan = formGroup.querySelector('.error-message');
            const validateAttr = input.dataset.validate;
            
            if (!validateAttr) return true;

            const rules = validateAttr.split('|');
            let isValid = true;
            let errorMessage = '';

            for (const rule of rules) {
                if (validationRules[rule]) {
                    const result = validationRules[rule].validate(input.value);
                    if (!result) {
                        isValid = false;
                        errorMessage = validationRules[rule].message;
                        break;
                    }
                }
            }

            // Handle dropdown-with-other wrapper
            const dropdownWrapper = input.closest('.dropdown-with-other');
            if (dropdownWrapper) {
                const selectElement = dropdownWrapper.querySelector('select');
                if (selectElement && selectElement.value === 'Other') {
                    // Check the text input instead
                    return validateField(input);
                } else if (selectElement && selectElement.value !== '' && selectElement.value !== 'Other') {
                    // Pre-selected value is valid
                    if (errorSpan) {
                        errorSpan.textContent = '';
                        formGroup.classList.remove('invalid');
                        input.classList.remove('invalid');
                    }
                    return true;
                }
            }

            if (errorSpan) {
                errorSpan.textContent = isValid ? '' : errorMessage;
            }
            
            if (isValid) {
                formGroup.classList.remove('invalid');
                input.classList.remove('invalid');
            } else {
                formGroup.classList.add('invalid');
                input.classList.add('invalid');
            }

            return isValid;
        }

        // Add event listeners for real-time validation
        inputs.forEach(input => {
            // Validate on blur
            input.addEventListener('blur', function() {
                validateField(this);
            });

            // Validate on input for immediate feedback (optional)
            input.addEventListener('input', function() {
                const formGroup = this.closest('.form-group');
                if (formGroup && formGroup.classList.contains('invalid')) {
                    validateField(this);
                }
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all inputs
            inputs.forEach(input => {
                // Skip hidden inputs in dropdown-with-other when select is active
                const dropdownWrapper = input.closest('.dropdown-with-other');
                if (dropdownWrapper) {
                    const selectElement = dropdownWrapper.querySelector('select');
                    if (selectElement && selectElement.value !== 'Other' && input.id.includes('Input')) {
                        return;
                    }
                }
                
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            // Check for at least one learning modality selected
            const learningMods = ['blended', 'modularPrint', 'modularDigital', 'online', 'homeschooling', 'educationalTv', 'radioBasedTv'];
            let hasModality = false;
            learningMods.forEach(mod => {
                const select = form.querySelector(`[name="${mod}"]`);
                if (select && select.value === '1') {
                    hasModality = true;
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                alert('Please correct the errors in the form before submitting.');
            }
        });

        // Auto-format phone numbers
        const phoneInputs = form.querySelectorAll('input[data-validate*="phone"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                e.target.value = value;
            });
        });

        // Auto-format LRN
        const lrnInput = form.querySelector('input[name="lrn"]');
        if (lrnInput) {
            lrnInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 12) value = value.slice(0, 12);
                e.target.value = value;
            });
        }
    });