<?php
include "../../Back_End_Files/PHP_Files/student_progress_validation_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);

$showSuccess = isset($_GET['success']) && $_GET['success'] === '1';
$approved = isset($_GET['approved']) ? (int) $_GET['approved'] : 0;
$rejected = isset($_GET['rejected']) ? (int) $_GET['rejected'] : 0;
$actions = isset($_GET['actions']) ? (int) $_GET['actions'] : 0;
$successMessage = "Validation complete. Approved: {$approved}, Rejected: {$rejected}, Final actions applied: {$actions}.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress Validation - Admin</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Student Progress Validation'); ?>
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
        <h1>Teacher Student Progress Validation</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">&larr; Back to Dashboard</a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Student Name</label>
                    <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="Search student...">
                </div>
                <div class="filter-group">
                    <label for="school_year">School Year</label>
                    <select id="school_year" name="school_year">
                        <?php foreach ($schoolYears as $year): ?>
                            <option value="<?php echo htmlspecialchars($year); ?>" <?php echo $schoolYearFilter === $year ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($year); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester">
                        <option value="1st Semester" <?php echo $semesterFilter === '1st Semester' ? 'selected' : ''; ?>>1st Semester</option>
                        <option value="2nd Semester" <?php echo $semesterFilter === '2nd Semester' ? 'selected' : ''; ?>>2nd Semester</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="approval_status">Approval Status</label>
                    <select id="approval_status" name="approval_status">
                        <option value="Pending" <?php echo $approvalFilter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $approvalFilter === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Rejected" <?php echo $approvalFilter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <a href="student_progress_validation_page.php" class="btn btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <form method="POST" action="../../Back_End_Files/PHP_Files/student_progress_validation_backend.php" onsubmit="return showLoadingModal()">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                        <th>No</th>
                        <th>Student</th>
                        <th>Grade / Section</th>
                        <th>Term</th>
                        <th>Teacher</th>
                        <th>Computed</th>
                        <th>Teacher Recommendation</th>
                        <th>Teacher Remarks</th>
                        <th>Decision</th>
                        <th>Admin Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records && $records->num_rows > 0): ?>
                        <?php $i = 1; ?>
                        <?php while ($row = $records->fetch_assoc()): ?>
                            <?php
                            $fullName = trim((string) $row['last_name'] . ', ' . (string) $row['first_name']);
                            $adminRemarksValue = trim((string) $row['admin_remarks']);
                            $adminRemarksPreview = $adminRemarksValue;
                            if (strlen($adminRemarksPreview) > 60) {
                                $adminRemarksPreview = substr($adminRemarksPreview, 0, 60) . '...';
                            }
                            $isReadonlyRemarks = $row['approval_status'] === 'Approved';
                            ?>
                            <tr class="remarks-row"
                                data-student-label="<?php echo htmlspecialchars($fullName); ?>"
                                data-remarks-readonly="<?php echo $isReadonlyRemarks ? '1' : '0'; ?>">
                                <td>
                                    <input type="checkbox"
                                           class="row-checkbox"
                                           name="selected_records[]"
                                           value="<?php echo (int) $row['promotion_status_id']; ?>"
                                           <?php echo $row['approval_status'] === 'Approved' ? 'disabled' : ''; ?>>
                                </td>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($fullName); ?><br>
                                    <small>LRN: <?php echo htmlspecialchars((string) $row['lrn']); ?></small>
                                </td>
                                <td>
                                    Grade <?php echo (int) $row['grade_level']; ?><br>
                                    <small><?php echo htmlspecialchars((string) $row['strand_name']); ?> - <?php echo htmlspecialchars((string) $row['section_name']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars((string) $row['semester']); ?><br>
                                    <small><?php echo htmlspecialchars((string) $row['school_year']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(trim((string) $row['teacher_first_name'] . ' ' . (string) $row['teacher_last_name'])); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['computed_status']); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['recommended_status']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars((string) $row['teacher_remarks'])); ?></td>
                                <td>
                                    <input type="hidden"
                                           name="decision[<?php echo (int) $row['promotion_status_id']; ?>]"
                                           class="decision-hidden-input"
                                           value="<?php echo htmlspecialchars((string) $row['approval_status'], ENT_QUOTES); ?>">
                                    <button type="button"
                                            class="status-cycle-btn decision-status-toggle"
                                            data-status-values="Pending|Approved|Rejected">
                                        <?php echo htmlspecialchars((string) $row['approval_status']); ?>
                                    </button>
                                </td>
                                <td>
                                    <input type="hidden"
                                           name="admin_remarks[<?php echo (int) $row['promotion_status_id']; ?>]"
                                           class="remarks-hidden-input"
                                           value="<?php echo htmlspecialchars($adminRemarksValue, ENT_QUOTES); ?>">
                                    <button type="button"
                                            class="btn btn-remarks-edit"
                                            data-remarks-title="Admin Remarks">
                                        <?php echo $isReadonlyRemarks ? 'View Remarks' : 'Add/Edit Remarks'; ?>
                                    </button>
                                    <div class="remarks-state">
                                        <span class="remarks-indicator <?php echo $adminRemarksValue !== '' ? 'remarks-has-value' : ''; ?>">
                                            <?php echo $adminRemarksValue !== '' ? 'Saved remarks' : 'No saved remarks'; ?>
                                        </span>
                                        <span class="remarks-preview">
                                            <?php echo $adminRemarksValue !== '' ? htmlspecialchars($adminRemarksPreview) : 'Click Add/Edit Remarks to input and save.'; ?>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="no-results">No teacher progress records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="validation-action-bar">
            <div class="validation-action-copy">
                <span class="validation-action-kicker">Batch Validation</span>
                <strong id="selectedValidationCount">0 records selected</strong>
                <p>Choose the records you want to approve or reject, then confirm the selected validation decisions.</p>
            </div>
            <button type="submit" name="confirm_validation" class="confirm-btn" id="confirmValidationBtn" disabled>Confirm Validation</button>
        </div>
    </form>

    <div id="loadingModal" class="loading-modal">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Applying validation...</p>
            <span class="loading-subtext">Updating approval and promotion records.</span>
        </div>
    </div>

    <div id="successModal" class="success-modal <?php echo $showSuccess ? 'active' : ''; ?>">
        <div class="success-content">
            <div class="success-icon">&#10004;</div>
            <p><?php echo htmlspecialchars($successMessage); ?></p>
            <button type="button" class="btn btn-success" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>

    <div id="remarksModal" class="remarks-modal">
        <div class="remarks-modal-content">
            <h3 id="remarksModalTitle">Edit Remarks</h3>
            <p id="remarksModalStudentName" class="remarks-modal-student">Student:</p>
            <textarea id="remarksModalInput" rows="5" placeholder="Type your remarks here..."></textarea>
            <div class="remarks-modal-actions">
                <button type="button" class="btn btn-save" id="saveRemarksBtn">Save</button>
                <button type="button" class="btn btn-reset" id="cancelRemarksBtn">Cancel</button>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/remarks_modal_helper.js"></script>
    <script>
        var selectAll = document.getElementById('selectAllCheckbox');
        var rowCheckboxes = Array.from(document.querySelectorAll('.row-checkbox:not(:disabled)'));
        var confirmValidationBtn = document.getElementById('confirmValidationBtn');
        var selectedValidationCount = document.getElementById('selectedValidationCount');

        function updateValidationSelectionState() {
            var selectedCount = rowCheckboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).length;

            if (selectedValidationCount) {
                selectedValidationCount.textContent = selectedCount + (selectedCount === 1 ? ' record selected' : ' records selected');
            }

            if (confirmValidationBtn) {
                confirmValidationBtn.disabled = selectedCount === 0;
            }

            if (selectAll) {
                selectAll.checked = rowCheckboxes.length > 0 && selectedCount === rowCheckboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.row-checkbox').forEach(function (cb) {
                    if (!cb.disabled) {
                        cb.checked = selectAll.checked;
                    }
                });
                updateValidationSelectionState();
            });
        }

        rowCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateValidationSelectionState);
        });

        function applyStatusButtonClass(button, value) {
            button.classList.remove('status-pending', 'status-approved', 'status-rejected');
            if (value === 'Approved') {
                button.classList.add('status-approved');
                return;
            }
            if (value === 'Rejected') {
                button.classList.add('status-rejected');
                return;
            }
            button.classList.add('status-pending');
        }

        document.querySelectorAll('.decision-status-toggle').forEach(function (button) {
            var hiddenInput = button.parentElement ? button.parentElement.querySelector('.decision-hidden-input') : null;
            if (!hiddenInput) {
                return;
            }

            var statuses = (button.dataset.statusValues || 'Pending|Approved|Rejected')
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
            var modal = document.getElementById('loadingModal');
            if (modal) {
                modal.classList.add('active');
            }
            return true;
        }

        function closeSuccessModal() {
            var modal = document.getElementById('successModal');
            if (modal) {
                modal.classList.remove('active');
            }
            var url = new URL(window.location.href);
            url.searchParams.delete('success');
            url.searchParams.delete('approved');
            url.searchParams.delete('rejected');
            url.searchParams.delete('actions');
            window.history.replaceState({}, document.title, url);
        }

        updateValidationSelectionState();
    </script>
</body>
</html>
