document.addEventListener('DOMContentLoaded', function () {
    var complianceFilter = document.getElementById('compliance_status');
    if (complianceFilter) {
        complianceFilter.addEventListener('change', function () {
            var form = document.getElementById('documentComplianceFilterForm');
            if (form) {
                form.submit();
            }
        });
    }
});

function resetDocumentComplianceFilters() {
    window.location.href = 'document_compliance_page.php';
}
