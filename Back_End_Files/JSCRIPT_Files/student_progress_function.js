(function () {
    var studentCheckboxes = Array.from(document.querySelectorAll('.student-checkbox:not(:disabled)'));
    var saveValidationBtn = document.getElementById('saveValidationBtn');
    var selectedSaveCount = document.getElementById('selectedSaveCount');

    function applyPromotionSelectClass(control) {
        if (!control) {
            return;
        }

        control.classList.remove('promote', 'graduate', 'retain', 'pending');

        var rawValue = '';
        if (typeof control.value === 'string') {
            rawValue = control.value;
        } else {
            rawValue = control.textContent || '';
        }

        var value = rawValue.toLowerCase();
        if (value.indexOf('promote') >= 0) {
            control.classList.add('promote');
        } else if (value.indexOf('graduate') >= 0) {
            control.classList.add('graduate');
        } else if (value.indexOf('incomplete') >= 0) {
            control.classList.add('retain');
        } else {
            control.classList.add('pending');
        }
    }

    function getStatusOptions(button, fallback) {
        var values = (button.getAttribute('data-status-values') || fallback || '')
            .split('|')
            .map(function (item) { return item.trim(); })
            .filter(Boolean);
        return values.length ? values : [fallback || 'Pending'];
    }

    function setButtonValue(button, hiddenInput, value) {
        if (!button || !hiddenInput) {
            return;
        }
        hiddenInput.value = value;
        if (button.disabled && button.getAttribute('data-auto-label')) {
            button.textContent = button.getAttribute('data-auto-label');
        } else {
            button.textContent = value;
        }
        applyPromotionSelectClass(button);
    }

    function cycleStatusButton(button, hiddenInput) {
        if (!button || !hiddenInput || button.disabled) {
            return;
        }

        var options = getStatusOptions(button, 'Pending');
        var currentIndex = options.indexOf(hiddenInput.value);
        var nextIndex = currentIndex >= 0 ? (currentIndex + 1) % options.length : 0;
        setButtonValue(button, hiddenInput, options[nextIndex]);
    }

    var selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.student-checkbox').forEach(function (checkbox) {
                if (!checkbox.disabled) {
                    checkbox.checked = selectAll.checked;
                }
            });
            updateSaveSelectionState();
        });
    }

    function updateSaveSelectionState() {
        var selectedCount = studentCheckboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).length;

        if (selectedSaveCount) {
            selectedSaveCount.textContent = selectedCount + (selectedCount === 1 ? ' student selected' : ' students selected');
        }

        if (saveValidationBtn) {
            saveValidationBtn.disabled = selectedCount === 0;
        }

        if (selectAll) {
            selectAll.checked = studentCheckboxes.length > 0 && selectedCount === studentCheckboxes.length;
        }
    }

    function buildRecommendationOptions(recommendationButton, recommendationInput, computedStatus) {
        if (!recommendationButton || !recommendationInput) {
            return;
        }

        var semester = recommendationButton.getAttribute('data-semester') || '';
        if (semester !== '2nd Semester') {
            recommendationButton.setAttribute('data-status-values', 'Pending');
            setButtonValue(recommendationButton, recommendationInput, recommendationInput.value || 'Pending');
            return;
        }

        var gradeLevel = parseInt(recommendationButton.getAttribute('data-grade-level') || '0', 10);
        var previousValue = recommendationInput.value || 'Pending';
        var options = [{ value: 'Pending', label: 'Pending' }];

        if (computedStatus === 'Complete') {
            if (gradeLevel === 11) {
                options.push({ value: 'Promote to Grade 12', label: 'Promote to Grade 12' });
            } else if (gradeLevel === 12) {
                options.push({ value: 'Graduate', label: 'Graduate' });
            }
        } else {
            options.push({ value: 'Incomplete', label: 'Incomplete' });
        }

        var isPreviousValueAvailable = options.some(function (optionData) {
            return optionData.value === previousValue;
        });

        recommendationButton.setAttribute(
            'data-status-values',
            options.map(function (optionData) { return optionData.value; }).join('|')
        );

        setButtonValue(recommendationButton, recommendationInput, isPreviousValueAvailable ? previousValue : 'Pending');
    }

    document.querySelectorAll('.computed-status-btn').forEach(function (button) {
        var row = button.closest('tr');
        var hiddenInput = row ? row.querySelector('.computed-status-input') : null;
        if (!hiddenInput) {
            return;
        }

        setButtonValue(button, hiddenInput, hiddenInput.value || 'Pending');

        button.addEventListener('click', function () {
            cycleStatusButton(button, hiddenInput);

            var recommendationButton = row.querySelector('.recommendation-status-btn');
            var recommendationInput = row.querySelector('.recommendation-status-input');
            var computedStatus = hiddenInput.value || 'Pending';
            row.setAttribute('data-computed-status', computedStatus);
            buildRecommendationOptions(recommendationButton, recommendationInput, computedStatus);
        });
    });

    document.querySelectorAll('.recommendation-status-btn').forEach(function (button) {
        var row = button.closest('tr');
        var hiddenInput = row ? row.querySelector('.recommendation-status-input') : null;
        if (!hiddenInput) {
            return;
        }

        setButtonValue(button, hiddenInput, hiddenInput.value || 'Pending');
        button.addEventListener('click', function () {
            cycleStatusButton(button, hiddenInput);
        });
    });

    document.querySelectorAll('#progressTable tbody tr').forEach(function (row) {
        var computedInput = row.querySelector('.computed-status-input');
        var recommendationButton = row.querySelector('.recommendation-status-btn');
        var recommendationInput = row.querySelector('.recommendation-status-input');
        if (!computedInput || !recommendationButton || !recommendationInput) {
            return;
        }

        function syncRecommendationOptions() {
            var computedStatus = computedInput.value || 'Pending';
            row.setAttribute('data-computed-status', computedStatus);
            buildRecommendationOptions(recommendationButton, recommendationInput, computedStatus);
        }

        syncRecommendationOptions();
    });

    function showSuccessModal(message) {
        var successModal = document.getElementById('successModal');
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.textContent = message;
        }
        if (successModal) {
            successModal.classList.add('active');
        }
    }

    function closeSuccessModal() {
        var successModal = document.getElementById('successModal');
        if (successModal) {
            successModal.classList.remove('active');
        }
    }

    window.showSuccessModal = showSuccessModal;
    window.closeSuccessModal = closeSuccessModal;

    window.addEventListener('click', function (event) {
        var successModal = document.getElementById('successModal');
        if (successModal && event.target === successModal) {
            closeSuccessModal();
        }
    });

    var promotionForm = document.getElementById('promotionForm');
    if (promotionForm) {
        promotionForm.addEventListener('submit', function (event) {
            var checked = document.querySelectorAll('.student-checkbox:checked');
            if (checked.length === 0) {
                alert('Please select at least one student.');
                event.preventDefault();
                return;
            }

            var loadingModal = document.getElementById('loadingModal');
            if (loadingModal) {
                loadingModal.classList.add('active');
            }
        });
    }

    var progressSearch = document.getElementById('progressSearch');
    var completionFilter = document.getElementById('progressCompletionFilter');
    var validationFilter = document.getElementById('progressValidationFilter');
    var applyFiltersBtn = document.getElementById('applyProgressFilters');
    var resetFiltersBtn = document.getElementById('resetProgressFilters');
    var progressRows = document.querySelectorAll('#progressTable tbody tr');
    var noMatchBox = document.getElementById('progressNoMatch');

    function applyProgressFilters() {
        if (!progressRows.length) {
            return;
        }

        var searchText = (progressSearch ? progressSearch.value : '').toLowerCase().trim();
        var completion = completionFilter ? completionFilter.value : '';
        var validation = validationFilter ? validationFilter.value : '';
        var visibleCount = 0;

        progressRows.forEach(function (row) {
            var rowName = (row.getAttribute('data-student-name') || '').toLowerCase();
            var rowCompletion = row.getAttribute('data-computed-status') || '';
            var rowValidation = row.getAttribute('data-approval-status') || '';

            var matchSearch = searchText === '' || rowName.indexOf(searchText) >= 0;
            var matchCompletion = completion === '' || rowCompletion === completion;
            var matchValidation = validation === '' || rowValidation === validation;

            var visible = matchSearch && matchCompletion && matchValidation;
            row.style.display = visible ? '' : 'none';
            if (visible) {
                visibleCount++;
            }
        });

        if (noMatchBox) {
            noMatchBox.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyProgressFilters);
    }

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function () {
            if (progressSearch) progressSearch.value = '';
            if (completionFilter) completionFilter.value = '';
            if (validationFilter) validationFilter.value = '';
            applyProgressFilters();
        });
    }

    if (progressSearch) {
        progressSearch.addEventListener('keyup', function (event) {
            if (event.key === 'Enter') {
                applyProgressFilters();
            }
        });
    }

    studentCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateSaveSelectionState);
    });

    updateSaveSelectionState();
})();
