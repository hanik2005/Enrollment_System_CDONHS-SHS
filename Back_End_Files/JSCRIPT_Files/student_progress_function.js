(function () {
    var selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(function (checkbox) {
                if (!checkbox.disabled) {
                    checkbox.checked = selectAll.checked;
                }
            });
        });
    }

    document.querySelectorAll('.promotion-select').forEach(function (select) {
        select.addEventListener('change', function () {
            select.classList.remove('promote', 'graduate', 'retain', 'pending');

            var value = (select.value || '').toLowerCase();
            if (value.indexOf('promote') >= 0) {
                select.classList.add('promote');
            } else if (value.indexOf('graduate') >= 0) {
                select.classList.add('graduate');
            } else if (value.indexOf('incomplete') >= 0) {
                select.classList.add('retain');
            } else {
                select.classList.add('pending');
            }
        });
        select.dispatchEvent(new Event('change'));
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
            var submitter = event.submitter;
            var isBulk = submitter && submitter.name === 'bulk_update_promotion';
            if (isBulk) {
                var checked = document.querySelectorAll('.student-checkbox:checked');
                if (checked.length === 0) {
                    alert('Please select at least one student.');
                    event.preventDefault();
                    return;
                }
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
})();
