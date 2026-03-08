<?php
include "../../Back_End_Files/PHP_Files/student_progress_validation_backend.php";

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
        <div class="center">
            Admin - Student Progress Validation
        </div>
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
                <a href="home.php">Home</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
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

    <form method="POST" action="../../Back_End_Files/PHP_Files/student_progress_validation_backend.php" onsubmit="showLoadingModal()">
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
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="row-checkbox"
                                           name="selected_records[]"
                                           value="<?php echo (int) $row['promotion_status_id']; ?>"
                                           <?php echo $row['approval_status'] === 'Approved' ? 'disabled' : ''; ?>>
                                </td>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?><br>
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
                                    <select name="decision[<?php echo (int) $row['promotion_status_id']; ?>]">
                                        <option value="Pending" <?php echo $row['approval_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Approved" <?php echo $row['approval_status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Rejected" <?php echo $row['approval_status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="admin_remarks[<?php echo (int) $row['promotion_status_id']; ?>]" rows="2" class="remarks-small"><?php echo htmlspecialchars((string) $row['admin_remarks']); ?></textarea>
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

        <div class="validation-buttons" style="padding: 0 20px 20px; text-align: right;">
            <button type="submit" name="confirm_validation" class="confirm-btn">Confirm Validation</button>
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

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
        <br>
        School Management System
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script>
        var selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.row-checkbox').forEach(function (cb) {
                    if (!cb.disabled) {
                        cb.checked = selectAll.checked;
                    }
                });
            });
        }

        function showLoadingModal() {
            var modal = document.getElementById('loadingModal');
            if (modal) {
                modal.classList.add('active');
            }
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
    </script>
</body>
</html>
