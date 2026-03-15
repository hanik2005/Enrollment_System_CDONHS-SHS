// ==============================
// Application List Functionality
// ==============================

document.addEventListener("DOMContentLoaded", function () {
    const REMARKS_STORAGE_KEY = "admin_app_remarks_";

    const selectAllCheckbox = document.getElementById("selectAllCheckbox");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");
    const confirmBatchBtn = document.getElementById("confirmBatchBtn");
    const selectedBatchCount = document.getElementById("selectedBatchCount");
    const loadingModal = document.getElementById("loadingModal");

    const remarksModal = document.getElementById("remarksModal");
    const remarksModalInput = document.getElementById("remarksModalInput");
    const remarksModalStudentName = document.getElementById("remarksModalStudentName");
    const saveRemarksBtn = document.getElementById("saveRemarksBtn");
    const cancelRemarksBtn = document.getElementById("cancelRemarksBtn");

    let activeRemarksRow = null;

    function getRowApplicationId(row) {
        return row?.dataset?.applicationId || "";
    }

    function getRemarksStorageKey(applicationId) {
        return REMARKS_STORAGE_KEY + applicationId;
    }

    function updateRemarksDisplay(row) {
        if (!row) return;
        const remarksField = row.querySelector(".batch-remarks");
        const indicator = row.querySelector(".remarks-indicator");
        const preview = row.querySelector(".remarks-preview");
        if (!remarksField || !indicator || !preview) return;

        const value = remarksField.value.trim();
        if (value !== "") {
            indicator.textContent = "Saved remarks";
            indicator.classList.add("remarks-has-value");
            preview.textContent = value.length > 60 ? value.substring(0, 60) + "..." : value;
        } else {
            indicator.textContent = "No saved remarks";
            indicator.classList.remove("remarks-has-value");
            preview.textContent = "Click Add/Edit Remarks to input and save.";
        }
    }

    function initializeRemarksFromStorage(row) {
        if (!row) return;
        const appId = getRowApplicationId(row);
        const remarksField = row.querySelector(".batch-remarks");
        if (!appId || !remarksField) return;

        const saved = localStorage.getItem(getRemarksStorageKey(appId));
        if (saved !== null) {
            remarksField.value = saved;
        }
        updateRemarksDisplay(row);
    }

    function toggleRowBatchFields(row, isChecked) {
        const batchFields = row?.querySelector(".batch-update-fields");
        const statusDisplay = row?.querySelector(".application-status-display");

        if (batchFields) {
            batchFields.style.display = isChecked ? "flex" : "none";
        }

        if (statusDisplay) {
            statusDisplay.classList.toggle("is-hidden", isChecked);
        }
    }

    function applyStatusButtonClass(button, value) {
        if (!button) return;
        button.classList.remove("status-pending", "status-approved", "status-rejected");
        if (value === "Approved") {
            button.classList.add("status-approved");
            return;
        }
        if (value === "Rejected") {
            button.classList.add("status-rejected");
            return;
        }
        button.classList.add("status-pending");
    }

    function updateStatusDisplay(row) {
        if (!row) return;

        const hiddenInput = row.querySelector(".batch-status");
        if (!hiddenInput) return;

        const value = hiddenInput.value || "Pending";
        const badge = row.querySelector(".application-status-display");
        const button = row.querySelector(".batch-status-toggle");

        if (badge) {
            badge.textContent = value;
            applyStatusButtonClass(badge, value);
        }

        if (button) {
            button.textContent = value;
            applyStatusButtonClass(button, value);
        }
    }

    function initializeBatchStatusToggle(button) {
        if (!button) return;
        const row = button.closest(".application-row");
        if (!row) return;

        const hiddenInput = row.querySelector(".batch-status");
        if (!hiddenInput) return;

        const statusesRaw = button.dataset.statusValues || "Pending|Approved|Rejected";
        const statuses = statusesRaw.split("|").map((item) => item.trim()).filter(Boolean);
        if (statuses.length === 0) return;

        if (!statuses.includes(hiddenInput.value)) {
            hiddenInput.value = statuses[0];
        }

        updateStatusDisplay(row);

        button.addEventListener("click", function () {
            const currentIndex = statuses.indexOf(hiddenInput.value);
            const nextIndex = currentIndex >= 0 ? (currentIndex + 1) % statuses.length : 0;
            const nextValue = statuses[nextIndex];
            hiddenInput.value = nextValue;
            updateStatusDisplay(row);
        });
    }

    function getEnabledCheckboxes() {
        return Array.from(rowCheckboxes).filter((checkbox) => !checkbox.disabled);
    }

    function updateBatchSelectionState() {
        const enabledCheckboxes = getEnabledCheckboxes();
        const selectedCount = enabledCheckboxes.filter((checkbox) => checkbox.checked).length;
        const allChecked = enabledCheckboxes.length > 0 && selectedCount === enabledCheckboxes.length;
        const someChecked = selectedCount > 0 && !allChecked;

        if (selectedBatchCount) {
            selectedBatchCount.textContent =
                selectedCount + (selectedCount === 1 ? " application selected" : " applications selected");
        }

        if (confirmBatchBtn) {
            confirmBatchBtn.disabled = selectedCount === 0;
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked;
        }
    }

    document.querySelectorAll(".application-row").forEach((row) => {
        initializeRemarksFromStorage(row);
    });

    document.querySelectorAll(".batch-status-toggle").forEach((button) => {
        initializeBatchStatusToggle(button);
    });

    // ==============================
    // REMARKS MODAL
    // ==============================
    function openRemarksModal(row) {
        if (!row || !remarksModal) return;
        activeRemarksRow = row;

        const studentName = row.dataset.studentName || "Student";
        const remarksField = row.querySelector(".batch-remarks");
        remarksModalStudentName.textContent = "Student: " + studentName;
        remarksModalInput.value = remarksField ? remarksField.value : "";
        remarksModal.classList.add("active");
        remarksModalInput.focus();
    }

    function closeRemarksModal() {
        if (!remarksModal) return;
        remarksModal.classList.remove("active");
        activeRemarksRow = null;
    }

    document.querySelectorAll(".btn-remarks-edit").forEach((button) => {
        button.addEventListener("click", function () {
            const row = this.closest(".application-row");
            openRemarksModal(row);
        });
    });

    if (saveRemarksBtn) {
        saveRemarksBtn.addEventListener("click", function () {
            if (!activeRemarksRow) return;
            const remarksField = activeRemarksRow.querySelector(".batch-remarks");
            const appId = getRowApplicationId(activeRemarksRow);
            const savedValue = remarksModalInput.value.trim();

            if (remarksField) {
                remarksField.value = savedValue;
            }

            if (appId) {
                if (savedValue !== "") {
                    localStorage.setItem(getRemarksStorageKey(appId), savedValue);
                } else {
                    localStorage.removeItem(getRemarksStorageKey(appId));
                }
            }

            updateRemarksDisplay(activeRemarksRow);
            closeRemarksModal();
        });
    }

    if (cancelRemarksBtn) {
        cancelRemarksBtn.addEventListener("click", closeRemarksModal);
    }

    window.addEventListener("click", function (event) {
        if (event.target === remarksModal) {
            closeRemarksModal();
        }
    });

    // ==============================
    // SELECT ALL CHECKBOX
    // ==============================
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const isChecked = this.checked;
            rowCheckboxes.forEach((checkbox) => {
                if (!checkbox.disabled) {
                    checkbox.checked = isChecked;
                    const row = checkbox.closest(".application-row");
                    toggleRowBatchFields(row, isChecked);
                }
            });
            updateBatchSelectionState();
        });
    }

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            const enabledCheckboxes = getEnabledCheckboxes();
            const allChecked = enabledCheckboxes.length > 0 && enabledCheckboxes.every((cb) => cb.checked);
            const someChecked = enabledCheckboxes.some((cb) => cb.checked);

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }

            const row = checkbox.closest(".application-row");
            toggleRowBatchFields(row, checkbox.checked);
            updateBatchSelectionState();
        });
    });

    // ==============================
    // CONFIRM SELECTED
    // ==============================
    if (confirmBatchBtn) {
        confirmBatchBtn.addEventListener("click", function () {
            const checkedCheckboxes = document.querySelectorAll(".row-checkbox:checked");

            if (checkedCheckboxes.length === 0) {
                alert("Please select at least one row to confirm.");
                return;
            }

            const updates = [];
            const lrnMissingStudents = [];
            let hasValidationError = false;

            checkedCheckboxes.forEach((checkbox) => {
                const row = checkbox.closest(".application-row");
                if (!row) return;

                if (row.dataset.hasLrn === "0") {
                    lrnMissingStudents.push(row.dataset.studentName || `Application #${row.dataset.applicationId}`);
                    hasValidationError = true;
                    return;
                }

                const applicationId = row.dataset.applicationId;
                const batchFields = row.querySelector(".batch-update-fields");
                if (!batchFields) return;

                const remarks = batchFields.querySelector(".batch-remarks")?.value.trim() || "";
                const status = batchFields.querySelector(".batch-status")?.value || "Pending";

                if (status === "Rejected" && remarks === "") {
                    hasValidationError = true;
                    return;
                }

                updates.push({
                    application_id: applicationId,
                    remarks: remarks,
                    status: status,
                    advisory: null
                });
            });

            if (hasValidationError) {
                if (lrnMissingStudents.length > 0) {
                    alert(
                        "Cannot confirm applications with missing LRN. Please edit the LRN first in Sensitive Information:\n- " +
                            lrnMissingStudents.join("\n- ")
                    );
                    return;
                }
                alert("Please enter remarks for all rejected applications.");
                return;
            }

            if (updates.length === 0) {
                alert("No applications to update.");
                return;
            }

            if (loadingModal) {
                loadingModal.classList.add("active");
            }

            fetch("../../Back_End_Files/PHP_Files/student_update_remarks.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ updates: updates })
            })
                .then((res) => res.json())
                .then((resp) => {
                    if (loadingModal) {
                        loadingModal.classList.remove("active");
                    }
                    if (resp.success) {
                        showSuccessModal(resp.message || "Applications updated successfully!");
                    } else {
                        alert(resp.message || "Failed to update applications.");
                    }
                })
                .catch(() => {
                    if (loadingModal) {
                        loadingModal.classList.remove("active");
                    }
                    alert("Error updating applications. Please try again.");
                });
        });
    }

    // ==============================
    // SUCCESS MODAL
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

    function closeSuccessModal(shouldReload = true) {
        const successModal = document.getElementById("successModal");
        if (successModal) {
            successModal.classList.remove("active");
        }
        if (shouldReload) {
            location.reload();
        } else {
            const url = new URL(window.location.href);
            url.searchParams.delete("success");
            window.history.replaceState({}, document.title, url);
        }
    }

    window.closeSuccessModal = closeSuccessModal;

    window.addEventListener("click", function (event) {
        const successModal = document.getElementById("successModal");
        if (successModal && event.target === successModal) {
            closeSuccessModal();
        }
    });

    updateBatchSelectionState();
});
