<?php
include "../../Back_End_Files/PHP_Files/admin_reports_dashboard_backend.php";
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports Dashboard - CDONHS-SHS</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/application_list_design.css">
    <link rel="stylesheet" href="../../Design/admin/admin_reports_dashboard_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Reports Dashboard'); ?>
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
        <h1>Enrollment and Approval Reports</h1>
    </div>

    <div class="nav-links">
        <a href="home.php">Back to Dashboard</a>
    </div>

    <div class="reports-toolbar">
        <form method="GET" id="schoolYearFilterForm">
            <label for="school_year">School Year:</label>
            <select name="school_year" id="school_year" onchange="applySchoolYearFilter()">
                <option value="All" <?php echo $selectedSchoolYear === 'All' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($availableSchoolYears as $schoolYear): ?>
                    <option value="<?php echo htmlspecialchars($schoolYear); ?>" <?php echo $selectedSchoolYear === $schoolYear ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($schoolYear); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-filter">Apply</button>
            <a href="reports_dashboard_page.php" class="btn btn-reset">Reset</a>
            <button type="button" class="btn btn-filter" onclick="printReportsDashboard()">Print</button>
        </form>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Enrolled Students (Active)</div>
            <div class="kpi-value"><?php echo (int) $totalEnrolled; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Pending Applications</div>
            <div class="kpi-value"><?php echo (int) $pendingApplications; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Pending Enlistment Approval</div>
            <div class="kpi-value"><?php echo (int) $pendingEnlistment; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Promoted Students</div>
            <div class="kpi-value"><?php echo (int) $promotedCount; ?></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Graduated Students</div>
            <div class="kpi-value"><?php echo (int) $graduatedCount; ?></div>
        </div>
    </div>

    <div class="report-grid">
        <div class="report-card">
            <h3 class="report-title">Enrolled Count by Grade Level</h3>
            <?php if (!empty($countsByGrade)): ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Grade Level</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countsByGrade as $row): ?>
                                <tr>
                                    <td><?php echo ((int) $row['grade_level'] > 0) ? 'Grade ' . (int) $row['grade_level'] : 'Unassigned'; ?></td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="report-empty">No data available.</div>
            <?php endif; ?>
        </div>

        <div class="report-card">
            <h3 class="report-title">Enrolled Count by Strand</h3>
            <?php if (!empty($countsByStrand)): ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Strand</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countsByStrand as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['strand_name']); ?></td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="report-empty">No data available.</div>
            <?php endif; ?>
        </div>

        <div class="report-card">
            <h3 class="report-title">Enrolled Count by Section</h3>
            <?php if (!empty($countsBySection)): ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countsBySection as $row): ?>
                                <tr>
                                    <td>
                                        <?php
                                        if ($row['section_name'] === 'Unassigned') {
                                            echo 'Unassigned';
                                        } else {
                                            echo 'Grade ' . (int) $row['grade_level'] . ' - ' . htmlspecialchars($row['strand_abbreviation']) . ' ' . htmlspecialchars($row['section_name']);
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="report-empty">No data available.</div>
            <?php endif; ?>
        </div>

        <div class="report-card">
            <h3 class="report-title">Application Status Breakdown</h3>
            <?php if (!empty($applicationStatusBreakdown)): ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Application Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applicationStatusBreakdown as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['application_status']); ?></td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="report-empty">No data available.</div>
            <?php endif; ?>
        </div>

        <div class="report-card">
            <h3 class="report-title">Enlistment Status Breakdown</h3>
            <?php if (!empty($enlistmentStatusBreakdown)): ?>
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Enlistment Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enlistmentStatusBreakdown as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['enlistment_status']); ?></td>
                                    <td><?php echo (int) $row['total']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="report-empty">No data available.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>

    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script src="../../Back_End_Files/JSCRIPT_Files/admin_reports_dashboard_function.js"></script>
</body>
</html>
