function printEnrollmentSummary() {
    window.print();
}

(function () {
    var searchInput = document.getElementById('summarySearch');
    var table = document.getElementById('summaryTable');

    if (!searchInput || !table) {
        return;
    }

    var bodyRows = table.querySelectorAll('tbody tr');
    searchInput.addEventListener('input', function () {
        var keyword = searchInput.value.toLowerCase().trim();

        bodyRows.forEach(function (row) {
            var text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(keyword) > -1 ? '' : 'none';
        });
    });
})();
