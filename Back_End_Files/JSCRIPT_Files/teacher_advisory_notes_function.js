(function () {
    var modal = document.getElementById('noteModal');
    var openButtons = document.querySelectorAll('.open-note-modal');
    var closeButton = document.getElementById('closeNoteModal');
    var cancelButton = document.getElementById('cancelNoteModal');

    var studentIdInput = document.getElementById('noteStudentId');
    var modalTitle = document.getElementById('noteModalTitle');
    var behaviorInput = document.getElementById('behavior_note');
    var followUpInput = document.getElementById('follow_up_note');
    var interventionInput = document.getElementById('intervention_note');

    function openModal() {
        if (!modal) return;
        modal.classList.add('active');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('active');
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (!studentIdInput || !modalTitle || !behaviorInput || !followUpInput || !interventionInput) {
                return;
            }

            studentIdInput.value = button.dataset.studentId || '';
            modalTitle.textContent = 'Advisory Notes: ' + (button.dataset.studentName || 'Student');
            behaviorInput.value = button.dataset.behaviorNote || '';
            followUpInput.value = button.dataset.followUpNote || '';
            interventionInput.value = button.dataset.interventionNote || '';

            openModal();
        });
    });

    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    var notesSearch = document.getElementById('notesSearch');
    var notesTable = document.getElementById('notesTable');
    if (notesSearch && notesTable) {
        var rows = notesTable.querySelectorAll('tbody tr');
        notesSearch.addEventListener('input', function () {
            var keyword = notesSearch.value.toLowerCase().trim();
            rows.forEach(function (row) {
                var rowText = row.textContent.toLowerCase();
                row.style.display = rowText.indexOf(keyword) > -1 ? '' : 'none';
            });
        });
    }
})();
