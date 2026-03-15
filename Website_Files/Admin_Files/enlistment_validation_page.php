<?php
include "../../Back_End_Files/PHP_Files/enlistment_validation_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);

$showSuccessModal = isset($_GET['success']) && $_GET['success'] === '1';
$successMessage = "Students validated successfully!";

if ($showSuccessModal) {
    $updatedCount = isset($_GET['updated']) ? (int) $_GET['updated'] : 0;
    $emailAttemptedCount = isset($_GET['emails_attempted']) ? (int) $_GET['emails_attempted'] : 0;
    $emailSentCount = isset($_GET['emails_sent']) ? (int) $_GET['emails_sent'] : 0;
    $emailFailedCount = isset($_GET['emails_failed']) ? (int) $_GET['emails_failed'] : 0;
    $emailSkippedCount = isset($_GET['emails_skipped']) ? (int) $_GET['emails_skipped'] : 0;

    $successMessage = "Validation completed for {$updatedCount} student(s).";

    if ($emailAttemptedCount > 0) {
        $successMessage .= " Email sent: {$emailSentCount}.";
        if ($emailFailedCount > 0) {
            $successMessage .= " Failed: {$emailFailedCount}.";
        } else {
            $successMessage .= " All required emails were sent successfully.";
        }
    }

    if ($emailSkippedCount > 0) {
        $successMessage .= " Skipped (no email found): {$emailSkippedCount}.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Enlistment Validation</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <link rel="stylesheet" href="../../Design/admin/enlistment_validation.css">
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONSHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Enlistment Validation'); ?>
        <div class="right">
            <button class="home-menu-toggle" type="button" data-profile-src="../../Assets/admin_profile.png" data-profile-alt="Admin profile">
                <span class="menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="menu-label">Menu</span>
            </button>
            <div class="legacy-nav-links">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['href']); ?>"<?php echo isset($link['class']) ? ' class="' . htmlspecialchars($link['class']) . '"' : ''; ?>>
                        <?php echo htmlspecialchars($link['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="page-title">
        <h1>Enlistment Validation</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">&larr; Back to Dashboard</a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Search by Name:</label>
                    <input type="text" id="search_name" name="search_name" placeholder="Enter student name..." value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '' ?>">
                </div>

                <div class="filter-group">
                    <label for="grade_level">Grade Level:</label>
                    <select id="grade_level" name="grade_level">
                        <option value="">Select Grade Level</option>
                        <option value="11" <?= ($grade == '11') ? 'selected' : '' ?>>11</option>
                        <option value="12" <?= ($grade == '12') ? 'selected' : '' ?>>12</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="strand_id">Strand:</label>
                    <select id="strand_id" name="strand_id" onchange="this.form.submit()">
                        <option value="">Select Strand</option>
                        <?php foreach ($strands as $s): ?>
                            <option value="<?= $s['strand_id'] ?>" <?= ($strand == $s['strand_id']) ? 'selected' : '' ?>>
                                <?= $s['strand_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="section_id">Section:</label>
                    <select id="section_id" name="section_id" <?= empty($strand) ? 'disabled' : '' ?>>
                        <option value="">Select Section</option>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= $sec['section_id'] ?>" <?= ($section == $sec['section_id']) ? 'selected' : '' ?>>
                                <?= $sec['section_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <a href="enlistment_validation_page.php" class="btn btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <form method="POST" action="../../Back_End_Files/PHP_Files/admin_enlistment_validation_backend.php" onsubmit="return showLoadingModal()">
        <div class="table-container">
            <table class="validation-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                        <th>No</th>
                        <th>LRN</th>
                        <th>Student Name</th>
                        <th>Enrollment Type</th>
                        <th>Grade Level</th>
                        <th>Strand</th>
                        <th>Section</th>
                        <th>Semester</th>
                        <th>Enlistment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $stmt = $connection->prepare($sql);

                    if (!empty($params)) {
                        $stmt->bind_param($types, ...$params);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $student_name = $row['first_name'] . ' ' . $row['last_name'];
                    ?>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" name="selected_students[]" value="<?= $row['student_id'] ?>"></td>
                            <td><?= $no++ ?></td>
                            <td><?= $row['lrn'] ?></td>
                            <td><?= $student_name ?></td>
                            <td><?= $row['enrollment_type'] ?? 'N/A' ?></td>
                            <td><?= $row['grade_level'] ?? '' ?></td>
                            <td><?= $row['strand_name'] ?? '' ?></td>
                            <td><?= $row['section_name'] ?? '' ?></td>
                            <td><?= htmlspecialchars((string) ($row['semester'] ?? '')) ?: 'N/A' ?></td>
                            <td>
                                <div class="validation-status-cell">
                                    <input type="hidden"
                                           name="status[<?= $row['student_id'] ?>]"
                                           class="status-dropdown status-hidden-input"
                                           value="<?= htmlspecialchars((string) $row['enlistment_status'], ENT_QUOTES) ?>">
                                    <button type="button"
                                            class="status-cycle-btn enlistment-status-toggle"
                                            data-status-values="Pending|Enlisted|Rejected">
                                        <?= htmlspecialchars((string) $row['enlistment_status']) ?>
                                    </button>
                                    <span class="validation-status-helper">Click the status button to change the enlistment status.</span>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="no-results">No pending enlistments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="validation-action-bar">
            <div class="validation-action-copy">
                <span class="validation-action-kicker">Batch Validation</span>
                <strong id="selectedValidationCount">0 students selected</strong>
                <p>Select the students you want to validate, then confirm the selected enlistment decisions.</p>
            </div>
            <button type="submit" name="confirm" class="confirm-btn" id="confirmValidationBtn" disabled>Confirm Validation</button>
        </div>
    </form>

    <div id="loadingModal" class="loading-modal">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Processing... Please wait.</p>
            <span class="loading-subtext">Sending notifications and updating records.</span>
        </div>
    </div>

    <div id="successModal" class="success-modal <?= $showSuccessModal ? 'active' : '' ?>">
        <div class="success-content">
            <div class="success-icon">&#10004;</div>
            <p id="successMessage"><?= htmlspecialchars($successMessage) ?></p>
            <button type="button" class="btn btn-success" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script>
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = Array.from(document.querySelectorAll('.row-checkbox'));
    const confirmValidationBtn = document.getElementById('confirmValidationBtn');
    const selectedValidationCount = document.getElementById('selectedValidationCount');

    function updateValidationSelectionState() {
        const selectedCount = rowCheckboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).length;

        if (selectedValidationCount) {
            selectedValidationCount.textContent = selectedCount + (selectedCount === 1 ? ' student selected' : ' students selected');
        }

        if (confirmValidationBtn) {
            confirmValidationBtn.disabled = selectedCount === 0;
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = rowCheckboxes.length > 0 && selectedCount === rowCheckboxes.length;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateValidationSelectionState();
        });
    }

    rowCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateValidationSelectionState);
    });

    function applyStatusButtonClass(button, value) {
        button.classList.remove('status-pending', 'status-approved', 'status-rejected');
        if (value === 'Enlisted') {
            button.classList.add('status-approved');
            return;
        }
        if (value === 'Rejected') {
            button.classList.add('status-rejected');
            return;
        }
        button.classList.add('status-pending');
    }

    document.querySelectorAll('.enlistment-status-toggle').forEach(function (button) {
        var hiddenInput = button.parentElement ? button.parentElement.querySelector('.status-hidden-input') : null;
        if (!hiddenInput) {
            return;
        }

        var statuses = (button.dataset.statusValues || 'Pending|Enlisted|Rejected')
            .split('|')
            .map(function (item) { return item.trim(); })
            .filter(Boolean);

        if (statuses.indexOf(hiddenInput.value) === -1) {
            hiddenInput.value = statuses[0] || 'Pending';
        }

        button.textContent = hiddenInput.value;
        applyStatusButtonClass(button, hiddenInput.value);

        button.addEventListener('click', function () {
            var currentIndex = statuses.indexOf(hiddenInput.value);
            var nextIndex = currentIndex >= 0 ? (currentIndex + 1) % statuses.length : 0;
            hiddenInput.value = statuses[nextIndex];
            button.textContent = hiddenInput.value;
            applyStatusButtonClass(button, hiddenInput.value);
        });
    });

    function showLoadingModal() {
        if (confirmValidationBtn && confirmValidationBtn.disabled) {
            return false;
        }

        const loadingModal = document.getElementById('loadingModal');
        if (loadingModal) {
            loadingModal.classList.add('active');
        }
        return true;
    }

    function closeSuccessModal() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            successModal.classList.remove('active');
        }

        const url = new URL(window.location.href);
        url.searchParams.delete('success');
        url.searchParams.delete('updated');
        url.searchParams.delete('emails_attempted');
        url.searchParams.delete('emails_sent');
        url.searchParams.delete('emails_failed');
        url.searchParams.delete('emails_skipped');
        window.history.replaceState({}, document.title, url);
    }

    window.addEventListener('click', function (event) {
        const successModal = document.getElementById('successModal');
        if (successModal && event.target === successModal) {
            closeSuccessModal();
        }
    });

    updateValidationSelectionState();
    </script>
</body>
</html>
