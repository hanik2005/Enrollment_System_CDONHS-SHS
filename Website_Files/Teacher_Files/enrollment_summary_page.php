<?php
include "../../Back_End_Files/PHP_Files/teacher_enrollment_summary_backend.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Enrollment Summary - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/teacher/teacher_enrollment_summary_design.css">
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderPortalHeaderBanner('Teacher Portal', 'Enrollment Summary', 'Advisory: ' . $advisoryText); ?>
        <div class="right">
            <button class="legacy-menu-trigger" type="button">
                <img src="<?php echo $profileImagePath; ?>" alt="Teacher Profile">
            </button>
            <div class="legacy-nav-links">
                <a href="home.php">Home</a>
                <a href="class_list.php">Class List</a>
                <a href="teacher_advisory_notes_page.php">Advisory Notes</a>
                <a href="settings.php">Settings</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="back-button-container">
        <a href="home.php" class="back-button">&larr; Back to Home</a>
    </div>

    <div class="summary-page">
        <div class="summary-title-card">
            <h1>Advisory Enrollment Snapshot</h1>
            <p>Quick summary of enlistment and document completeness in your advisory class.</p>
        </div>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-label">Total Advisory Students</div>
                <div class="summary-value"><?php echo (int) $summaryStats['total_students']; ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Enlisted</div>
                <div class="summary-value"><?php echo (int) $summaryStats['enlisted']; ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Pending</div>
                <div class="summary-value"><?php echo (int) $summaryStats['pending']; ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Missing Docs</div>
                <div class="summary-value"><?php echo (int) $summaryStats['missing_docs']; ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Promoted</div>
                <div class="summary-value"><?php echo (int) $summaryStats['promoted']; ?></div>
            </div>
        </div>

        <div class="summary-tools">
            <input type="text" id="summarySearch" placeholder="Search student, LRN, status...">
            <button type="button" class="btn-action" onclick="printEnrollmentSummary()">Print</button>
        </div>

        <?php if (empty($advisorySectionId)): ?>
            <div class="summary-empty">
                No advisory section is assigned to your account yet.
            </div>
        <?php elseif (empty($summaryRows)): ?>
            <div class="summary-empty">
                No students found in your advisory section.
            </div>
        <?php else: ?>
            <div class="summary-table-wrap">
                <table class="summary-table" id="summaryTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Student Name</th>
                            <th>LRN</th>
                            <th>Grade</th>
                            <th>Enlistment Status</th>
                            <th>Missing Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        <?php foreach ($summaryRows as $row): ?>
                            <?php
                            $statusClass = 'status-default';
                            if (strcasecmp($row['enlistment_status'], 'Enlisted') === 0) {
                                $statusClass = 'status-enlisted';
                            } elseif (strcasecmp($row['enlistment_status'], 'Pending') === 0) {
                                $statusClass = 'status-pending';
                            } elseif (strcasecmp($row['enlistment_status'], 'Promoted') === 0) {
                                $statusClass = 'status-promoted';
                            }
                            $missingClass = ((int) $row['missing_docs_count'] > 0) ? 'missing-has' : 'missing-complete';
                            ?>
                            <tr>
                                <td><?php echo $index++; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['lrn']); ?></td>
                                <td>Grade <?php echo (int) $row['grade_level']; ?></td>
                                <td>
                                    <span class="status-pill <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($row['enlistment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="missing-pill <?php echo $missingClass; ?>">
                                        <?php echo htmlspecialchars($row['missing_docs_text']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/teacher_enrollment_summary_function.js"></script>
</body>
</html>
