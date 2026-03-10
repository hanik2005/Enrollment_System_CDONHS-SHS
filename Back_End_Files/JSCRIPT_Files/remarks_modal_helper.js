document.addEventListener("DOMContentLoaded", function () {
    var remarksModal = document.getElementById("remarksModal");
    var remarksModalTitle = document.getElementById("remarksModalTitle");
    var remarksModalInput = document.getElementById("remarksModalInput");
    var remarksModalStudentName = document.getElementById("remarksModalStudentName");
    var saveRemarksBtn = document.getElementById("saveRemarksBtn");
    var cancelRemarksBtn = document.getElementById("cancelRemarksBtn");

    if (!remarksModal || !remarksModalInput || !remarksModalStudentName) {
        return;
    }

    var activeRemarksRow = null;
    var activeReadonly = false;

    function updateRemarksDisplay(row) {
        if (!row) {
            return;
        }

        var remarksField = row.querySelector(".remarks-hidden-input");
        var indicator = row.querySelector(".remarks-indicator");
        var preview = row.querySelector(".remarks-preview");

        if (!remarksField || !indicator || !preview) {
            return;
        }

        var value = remarksField.value.trim();
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

    function closeRemarksModal() {
        remarksModal.classList.remove("active");
        activeRemarksRow = null;
        activeReadonly = false;
    }

    function openRemarksModal(button) {
        var row = button.closest(".remarks-row");
        if (!row) {
            return;
        }

        var remarksField = row.querySelector(".remarks-hidden-input");
        var studentLabel = row.dataset.studentLabel || "Student";
        var remarksTitle = button.dataset.remarksTitle || "Edit Remarks";

        activeRemarksRow = row;
        activeReadonly = row.dataset.remarksReadonly === "1";

        remarksModalTitle.textContent = activeReadonly ? "View Remarks" : remarksTitle;
        remarksModalStudentName.textContent = "Student: " + studentLabel;
        remarksModalInput.value = remarksField ? remarksField.value : "";
        remarksModalInput.readOnly = activeReadonly;

        if (saveRemarksBtn) {
            saveRemarksBtn.style.display = activeReadonly ? "none" : "";
        }

        if (cancelRemarksBtn) {
            cancelRemarksBtn.textContent = activeReadonly ? "Close" : "Cancel";
        }

        remarksModal.classList.add("active");
        remarksModalInput.focus();
    }

    document.querySelectorAll(".remarks-row").forEach(updateRemarksDisplay);

    document.querySelectorAll(".btn-remarks-edit").forEach(function (button) {
        button.addEventListener("click", function () {
            openRemarksModal(button);
        });
    });

    if (saveRemarksBtn) {
        saveRemarksBtn.addEventListener("click", function () {
            if (!activeRemarksRow || activeReadonly) {
                closeRemarksModal();
                return;
            }

            var remarksField = activeRemarksRow.querySelector(".remarks-hidden-input");
            if (remarksField) {
                remarksField.value = remarksModalInput.value.trim();
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
});
