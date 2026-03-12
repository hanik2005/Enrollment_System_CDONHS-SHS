<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

include_once "../../Back_End_Files/PHP_Files/check_activation.php";
if (!isFeatureEnabled('Student Progress Page')) {
    header("Location: ../access_denied.php?feature=Student Progress Page");
    exit;
}

$stmt = $connection->prepare("
    SELECT u.*, t.first_name, t.last_name, t.middle_name, t.extension_name
    FROM users u
    INNER JOIN teachers t ON t.user_id = u.user_id
    WHERE u.user_id = ? AND u.role_id = 3
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$profileImagePath = "../../Assets/profile_button.png";

include "../../Back_End_Files/PHP_Files/student_progress_backend.php";
include "../../Back_End_Files/PHP_Files/student_promotion_backend.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/teacher/student_progress_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <title>Student Progress - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderPortalHeaderBanner('Teacher Portal', 'Student Progress', 'Advisory: ' . $advisoryText); ?>
        <div class="right">
            <button class="home-menu-toggle" type="button" data-profile-src="<?php echo $profileImagePath; ?>" data-profile-alt="Teacher profile">
                <span class="menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="menu-label">Menu</span>
            </button>
            <div class="legacy-nav-links">
                <a href="home.php">Home</a>
                <a href="class_list.php">Class List</a>
                <a href="enrollment_summary_page.php">Enrollment Summary</a>
                <a href="teacher_advisory_notes_page.php">Advisory Notes</a>
                
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="back-button-container">
        <a href="home.php" class="back-button">&larr; Back to Home</a>
    </div>

    <div class="progress-container">
        <?php if (!empty($message)): ?>
            <?php if ($message_type === 'success'): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    showSuccessModal('<?php echo addslashes($message); ?>');
                });
            </script>
            <?php else: ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="table-header">
            <h2>Student Progress Recommendation</h2>
            <p class="subtitle">
                Selected Term: <?php echo htmlspecialchars($progressSelectedSemester); ?> (<?php echo htmlspecialchars($progressSelectedSchoolYear); ?>)
            </p>
            <?php if ($progressSelectedSemester === '1st Semester'): ?>
                <p class="advisory-info">
                    Semester 1 rule: teacher sets Complete/Incomplete only. Complete means Promote to 2nd Semester (for admin approval).
                </p>
            <?php else: ?>
                <p class="advisory-info">
                    Semester 2 rule: set completion first, then recommend Promote/Graduate for complete students.
                </p>
            <?php endif; ?>
        </div>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="school_year">School Year</label>
                        <select name="school_year" id="school_year">
                            <?php foreach ($progressAvailableSchoolYears as $schoolYearOption): ?>
                                <option value="<?php echo htmlspecialchars($schoolYearOption); ?>" <?php echo $progressSelectedSchoolYear === $schoolYearOption ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($schoolYearOption); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="semester">Semester</label>
                        <select name="semester" id="semester">
                            <option value="1st Semester" <?php echo $progressSelectedSemester === '1st Semester' ? 'selected' : ''; ?>>1st Semester</option>
                            <option value="2nd Semester" <?php echo $progressSelectedSemester === '2nd Semester' ? 'selected' : ''; ?>>2nd Semester</option>
                        </select>
                    </div>
                    <div class="filter-buttons">
                        <button type="submit" class="btn btn-filter">View Term</button>
                        <a href="student_progress_page.php" class="btn btn-reset">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="filter-section">
            <div class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="progressSearch">Search Student</label>
                        <input type="text" id="progressSearch" placeholder="Type student name...">
                    </div>
                    <div class="filter-group">
                        <label for="progressCompletionFilter">Completion Status</label>
                        <select id="progressCompletionFilter">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Complete">Complete</option>
                            <option value="Incomplete">Incomplete</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="progressValidationFilter">Admin Validation</label>
                        <select id="progressValidationFilter">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="filter-buttons">
                        <button type="button" class="btn btn-filter" id="applyProgressFilters">Apply</button>
                        <button type="button" class="btn btn-reset" id="resetProgressFilters">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($progressData)): ?>
            <div class="no-results">
                <p>No students found in your advisory section for <?php echo htmlspecialchars($progressSelectedSemester); ?>.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <form method="POST" id="promotionForm">
                    <input type="hidden" name="selected_school_year" value="<?php echo htmlspecialchars($progressSelectedSchoolYear); ?>">
                    <input type="hidden" name="selected_semester" value="<?php echo htmlspecialchars($progressSelectedSemester); ?>">
                    <table class="progress-table" id="progressTable">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">
                                    <input type="checkbox" id="selectAll" title="Select All">
                                </th>
                                <th>No</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Semester</th>
                                <th>Completion Status</th>
                                <th>Recommendation</th>
                                <th>Teacher Remarks</th>
                                <th>Admin Validation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; ?>
                            <?php foreach ($progressData as $student): ?>
                                <?php
                                $fullName = $student['last_name'] . ', ' . $student['first_name'];
                                if (!empty($student['middle_name'])) {
                                    $fullName .= ' ' . substr($student['middle_name'], 0, 1) . '.';
                                }
                                if (!empty($student['extension_name'])) {
                                    $fullName .= ' ' . $student['extension_name'];
                                }

                                $computedStatus = (string) ($student['computed_status'] ?? 'Pending');
                                $recommendedStatus = (string) ($student['recommended_status'] ?? 'Pending');
                                $approvalStatus = (string) ($student['approval_status'] ?? 'Pending');
                                $isApproved = (int) ($student['is_approved'] ?? 0) === 1;
                                $gradeLevel = (int) ($student['grade_level'] ?? 0);

                                $recommendationClass = strtolower(str_replace([' ', 'to'], ['-', ''], $recommendedStatus));
                                $selectDisabled = $isApproved ? 'disabled' : '';
                                ?>
                                <tr
                                    class="remarks-row"
                                    data-student-name="<?php echo htmlspecialchars(strtolower($fullName)); ?>"
                                    data-student-label="<?php echo htmlspecialchars($fullName); ?>"
                                    data-remarks-readonly="<?php echo $isApproved ? '1' : '0'; ?>"
                                    data-computed-status="<?php echo htmlspecialchars($computedStatus); ?>"
                                    data-approval-status="<?php echo htmlspecialchars($approvalStatus); ?>"
                                >
                                    <td style="text-align: center;">
                                        <input type="checkbox"
                                               name="selected_students[]"
                                               value="<?php echo (int) $student['student_id']; ?>"
                                               class="student-checkbox"
                                               <?php echo $isApproved ? 'disabled' : ''; ?>>
                                    </td>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo htmlspecialchars($fullName); ?></td>
                                    <td>Grade <?php echo (int) $gradeLevel; ?></td>
                                    <td><?php echo htmlspecialchars((string) $student['semester']); ?></td>
                                    <td>
                                        <input type="hidden"
                                               name="students[<?php echo (int) $student['student_id']; ?>][computed_status]"
                                               class="computed-status-input"
                                               value="<?php echo htmlspecialchars($computedStatus, ENT_QUOTES); ?>">
                                        <button type="button"
                                                class="status-cycle-btn promotion-select computed-status-btn"
                                                data-status-values="Pending|Complete|Incomplete"
                                                <?php echo $selectDisabled; ?>>
                                            <?php echo htmlspecialchars($computedStatus); ?>
                                        </button>
                                    </td>
                                    <td>
                                        <input type="hidden" name="students[<?php echo (int) $student['student_id']; ?>][student_id]" value="<?php echo (int) $student['student_id']; ?>">
                                        <input type="hidden" name="students[<?php echo (int) $student['student_id']; ?>][grade_level]" value="<?php echo (int) $gradeLevel; ?>">

                                        <?php if ($progressSelectedSemester === '1st Semester'): ?>
                                            <input type="hidden"
                                                   name="students[<?php echo (int) $student['student_id']; ?>][recommended_status]"
                                                   class="recommendation-status-input"
                                                   value="Pending">
                                            <button type="button"
                                                    class="status-cycle-btn promotion-select recommendation-status-btn pending"
                                                    data-status-values="Pending"
                                                    data-auto-label="Auto: Promote to 2nd Semester / Incomplete"
                                                    data-grade-level="<?php echo (int) $gradeLevel; ?>"
                                                    data-semester="<?php echo htmlspecialchars($progressSelectedSemester); ?>"
                                                    disabled>
                                                Auto: Promote to 2nd Semester / Incomplete
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden"
                                                   name="students[<?php echo (int) $student['student_id']; ?>][recommended_status]"
                                                   class="recommendation-status-input"
                                                   value="<?php echo htmlspecialchars($recommendedStatus, ENT_QUOTES); ?>">
                                            <button type="button"
                                                    class="status-cycle-btn promotion-select recommendation-status-btn <?php echo htmlspecialchars($recommendationClass); ?>"
                                                    data-grade-level="<?php echo (int) $gradeLevel; ?>"
                                                    data-semester="<?php echo htmlspecialchars($progressSelectedSemester); ?>"
                                                    <?php echo $selectDisabled; ?>>
                                                <?php echo htmlspecialchars($recommendedStatus); ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $teacherRemarks = trim((string) ($student['teacher_remarks'] ?? ''));
                                        $teacherRemarksPreview = $teacherRemarks;
                                        if (strlen($teacherRemarksPreview) > 60) {
                                            $teacherRemarksPreview = substr($teacherRemarksPreview, 0, 60) . '...';
                                        }
                                        ?>
                                        <input type="hidden"
                                               name="students[<?php echo (int) $student['student_id']; ?>][teacher_remarks]"
                                               class="remarks-hidden-input"
                                               value="<?php echo htmlspecialchars($teacherRemarks, ENT_QUOTES); ?>">
                                        <button type="button"
                                                class="btn btn-remarks-edit"
                                                data-remarks-title="Teacher Remarks">
                                            <?php echo $isApproved ? 'View Remarks' : 'Add/Edit Remarks'; ?>
                                        </button>
                                        <div class="remarks-state">
                                            <span class="remarks-indicator <?php echo $teacherRemarks !== '' ? 'remarks-has-value' : ''; ?>">
                                                <?php echo $teacherRemarks !== '' ? 'Saved remarks' : 'No saved remarks'; ?>
                                            </span>
                                            <span class="remarks-preview">
                                                <?php echo $teacherRemarks !== '' ? htmlspecialchars($teacherRemarksPreview) : 'Click Add/Edit Remarks to input and save.'; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($approvalStatus === 'Approved'): ?>
                                            <span class="saved-badge">Approved</span>
                                        <?php elseif ($approvalStatus === 'Rejected'): ?>
                                            <span class="pending-badge">Rejected</span>
                                        <?php else: ?>
                                            <span style="color:#6b7280;font-size:12px;">Pending Admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div id="progressNoMatch" class="no-results" style="display:none; margin-top:12px;">
                        <p>No students match the current filters.</p>
                    </div>

                    <div class="validation-action-bar">
                        <div class="validation-action-copy">
                            <span class="validation-action-kicker">Teacher Submission</span>
                            <strong id="selectedSaveCount">0 students selected</strong>
                            <p>Select the students you want to submit, then save the selected recommendations for admin validation.</p>
                        </div>
                        <button type="submit" name="save_selected_students" class="confirm-btn" id="saveValidationBtn" disabled>Save for Admin Validation</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <div id="loadingModal" class="loading-modal">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Processing... Please wait.</p>
            <span class="loading-subtext">Saving recommendations for admin validation.</span>
        </div>
    </div>

    <div id="successModal" class="success-modal">
        <div class="success-content">
            <div class="success-icon">&#10004;</div>
            <p id="successMessage">Operation completed successfully.</p>
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

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js')); ?>"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/remarks_modal_helper.js?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../Back_End_Files/JSCRIPT_Files/remarks_modal_helper.js')); ?>"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/student_progress_function.js?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../Back_End_Files/JSCRIPT_Files/student_progress_function.js')); ?>"></script>
</body>
</html>
