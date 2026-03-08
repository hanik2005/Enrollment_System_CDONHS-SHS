function openEmailModal(applicationId, documentField, documentLabel, studentName) {
    var modal = document.getElementById('emailModal');
    var target = document.getElementById('emailModalTarget');

    document.getElementById('email_application_id').value = applicationId;
    document.getElementById('email_document_field').value = documentField;

    target.textContent = 'Student: ' + studentName + ' | Document: ' + documentLabel;
    modal.classList.add('active');
}

function closeEmailModal() {
    var modal = document.getElementById('emailModal');
    modal.classList.remove('active');
}

function openDeleteModal(applicationId, documentField, documentLabel, studentName) {
    var modal = document.getElementById('deleteModal');
    var target = document.getElementById('deleteModalTarget');

    document.getElementById('delete_application_id').value = applicationId;
    document.getElementById('delete_document_field').value = documentField;

    target.textContent = 'Delete ' + documentLabel + ' for ' + studentName + '? This cannot be undone.';
    modal.classList.add('active');
}

function closeDeleteModal() {
    var modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
}

window.addEventListener('click', function (event) {
    var emailModal = document.getElementById('emailModal');
    var deleteModal = document.getElementById('deleteModal');

    if (event.target === emailModal) {
        closeEmailModal();
    }

    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});
