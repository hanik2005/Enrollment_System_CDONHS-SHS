<?php
session_start();

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);

include "../../Back_End_Files/PHP_Files/joining_application_validation.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Applications - CDONHS-SHS Admin</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Student Applications'); ?>
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
        <h1>Student Application List</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">&larr; Back to Dashboard</a>
    </div>

    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search_name">Search by Name:</label>
                    <input type="text" id="search_name" name="search_name"
                           placeholder="Enter student name..."
                           value="<?= htmlspecialchars($search_name); ?>">
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-filter">Search</button>
                    <a href="admin_student_application_list.php" class="btn btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>LRN</th>
                    <th>Email</th>
                    <th>PSA Birth Cert</th>
                    <th>Form 138</th>
                    <th>Student ID</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $count = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $hasLrn = !empty(trim((string) ($row['lrn'] ?? '')));
                        $studentFullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        $existingRemarks = trim((string) ($row['remarks'] ?? ''));
                        $remarksPreview = $existingRemarks;
                        if (strlen($remarksPreview) > 60) {
                            $remarksPreview = substr($remarksPreview, 0, 60) . '...';
                        }
                        ?>
                        <tr class="application-row" data-application-id="<?= $row['application_id']; ?>" data-has-lrn="<?= $hasLrn ? '1' : '0'; ?>" data-student-name="<?= htmlspecialchars($studentFullName); ?>">
                            <td>
                                <input type="checkbox" class="row-checkbox" <?= $hasLrn ? '' : 'disabled'; ?> title="<?= $hasLrn ? 'Select for batch update' : 'LRN is required before confirming this application'; ?>">
                            </td>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td>
                                <?php if ($hasLrn): ?>
                                    <?= htmlspecialchars($row['lrn']); ?>
                                <?php else: ?>
                                    <div class="lrn-missing-container">
                                        <span class="doc-missing">Missing LRN</span>
                                        <span class="lrn-admin-note">Admin must edit the LRN in Sensitive Information.</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['email']); ?></td>

                            <td>
                                <?php if (!empty($row['psa_birth_certificate'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['psa_birth_certificate']); ?>"
                                       target="_blank" class="doc-submitted">&#10003; View</a>
                                <?php else: ?>
                                    <span class="doc-missing">&#10007; Missing</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($row['form_138'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['form_138']); ?>"
                                       target="_blank" class="doc-submitted">&#10003; View</a>
                                <?php else: ?>
                                    <span class="doc-missing">&#10007; Missing</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($row['student_id_copy'])): ?>
                                    <a href="../../uploads/Documents/student/<?= htmlspecialchars($row['student_id_copy']); ?>"
                                       target="_blank" class="doc-submitted">&#10003; View</a>
                                <?php else: ?>
                                    <span class="doc-missing">&#10007; Missing</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                $statusClass = 'status-pending';
                                if ($row['application_status'] === 'Approved') {
                                    $statusClass = 'status-approved';
                                }
                                if ($row['application_status'] === 'Rejected') {
                                    $statusClass = 'status-rejected';
                                }
                                ?>
                                <span class="status-badge <?= $statusClass; ?>">
                                    <?= htmlspecialchars($row['application_status']); ?>
                                </span>
                            </td>

                            <td>
                                <div class="batch-update-fields" style="display: none;">
                                    <input type="hidden" class="application-id" value="<?= $row['application_id']; ?>">
                                    <input type="hidden" name="remarks" class="batch-remarks" value="<?= htmlspecialchars($existingRemarks, ENT_QUOTES); ?>">
                                    <input type="hidden" name="application_status" class="batch-status" value="<?= htmlspecialchars((string) $row['application_status'], ENT_QUOTES); ?>">
                                    <button type="button"
                                            class="status-cycle-btn batch-status-toggle"
                                            data-status-values="Pending|Approved|Rejected">
                                        <?= htmlspecialchars((string) $row['application_status']); ?>
                                    </button>
                                </div>
                                <div class="original-form-fields">
                                    <button type="button" class="btn btn-remarks-edit">Add/Edit Remarks</button>
                                    <div class="remarks-state">
                                        <span class="remarks-indicator <?= $existingRemarks !== '' ? 'remarks-has-value' : ''; ?>">
                                            <?= $existingRemarks !== '' ? 'Saved remarks' : 'No saved remarks'; ?>
                                        </span>
                                        <span class="remarks-preview">
                                            <?= $existingRemarks !== '' ? htmlspecialchars($remarksPreview) : 'Click Add/Edit Remarks to input and save.'; ?>
                                        </span>
                                    </div>
                                    <span style="color: #666; font-size: 0.85em;">Use checkbox + Confirm Selected to apply saved remarks/status</span>
                                    <?php if (!$hasLrn): ?>
                                        <span class="lrn-admin-note-block">Cannot confirm while LRN is missing.</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td>
                                <a href="sensitive_information.php?search_name=<?= urlencode($row['first_name'] . ' ' . $row['last_name']); ?>"
                                   class="btn-view-sensitive"
                                   title="View Sensitive Information"
                                   target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px;">
                            No student applications found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="validation-action-bar">
        <div class="validation-action-copy">
            <span class="validation-action-kicker">Batch Validation</span>
            <strong id="selectedBatchCount">0 applications selected</strong>
            <p>Select the applications you want to process, then confirm the selected remarks and status updates.</p>
        </div>
        <button type="button" class="confirm-btn" id="confirmBatchBtn" disabled>Confirm Selected</button>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <div id="loadingModal" class="loading-modal">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Processing... Please wait.</p>
            <span class="loading-subtext">Sending notifications and updating records.</span>
        </div>
    </div>

    <div id="successModal" class="success-modal">
        <div class="success-content">
            <div class="success-icon">&#10003;</div>
            <p id="successMessage">Operation completed successfully!</p>
            <button type="button" class="btn btn-success" onclick="closeSuccessModal()">OK</button>
        </div>
    </div>

    <div id="remarksModal" class="remarks-modal">
        <div class="remarks-modal-content">
            <h3>Edit Remarks</h3>
            <p id="remarksModalStudentName" class="remarks-modal-student">Student:</p>
            <textarea id="remarksModalInput" rows="5" placeholder="Type your remarks here..."></textarea>
            <div class="remarks-modal-actions">
                <button type="button" class="btn btn-save" id="saveRemarksBtn">Save</button>
                <button type="button" class="btn btn-reset" id="cancelRemarksBtn">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js?v=<?= urlencode((string) @filemtime(__DIR__ . '/../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js')); ?>"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/application_list_function.js?v=<?= urlencode((string) @filemtime(__DIR__ . '/../../Back_End_Files/JSCRIPT_Files/application_list_function.js')); ?>"></script>
</body>
</html>
