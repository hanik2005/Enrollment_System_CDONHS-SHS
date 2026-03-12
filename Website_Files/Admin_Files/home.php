<?php
include "../../Back_End_Files/PHP_Files/admin_reports_dashboard_backend.php";

$profileImagePath = "../../Assets/admin_profile.png";
$displayName = $admin['username'] ?? ($admin['email'] ?? ($admin['first_name'] ?? "Admin User"));
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
$facebookPageUrl = "https://www.facebook.com/CDONHSSrHigh";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/admin_reports_dashboard_design.css">
</head>
<body class="admin-home-page">
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Home Dashboard'); ?>
    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="admin-home-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<div id="admin-home-menu" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Admin navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="<?php echo $profileImagePath; ?>" alt="Admin profile">
            <div>
                <h3><?php echo htmlspecialchars($displayName); ?></h3>
                <p><?php echo htmlspecialchars($adminRoleLabel); ?></p>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Admin page links">
            <?php foreach ($navLinks as $link): ?>
                <a href="<?php echo htmlspecialchars($link['href']); ?>"<?php echo isset($link['class']) ? ' class="' . htmlspecialchars($link['class']) . '"' : ''; ?>>
                    <?php echo htmlspecialchars($link['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
</div>

<main id="main-content">
    <div class="dashboard">
        <div class="dashboard-box home-dashboard-box">
            <section class="admin-home-hero">
                <div class="admin-home-hero-copy">
                    <span class="admin-home-badge"><?php echo htmlspecialchars($adminRoleLabel); ?></span>
                    <h1>Enrollment command center for <?php echo htmlspecialchars($displayName); ?></h1>
                    <p>Track application volume, validation workload, and enrollment progress from one place with the school's official blue-and-gold portal theme.</p>
                    <div class="admin-home-chip-row">
                        <span class="admin-home-chip">School Year: <?php echo htmlspecialchars($selectedSchoolYear); ?></span>
                        <span class="admin-home-chip">Pending Applications: <?php echo (int) $pendingApplications; ?></span>
                        <span class="admin-home-chip">Pending Enlistment: <?php echo (int) $pendingEnlistment; ?></span>
                    </div>
                    <div class="admin-home-action-row">
                        <a href="admin_student_application_list.php" class="admin-home-primary-link">Review Applications</a>
                        <a href="enlistment_validation_page.php" class="home-secondary-link">Open Enlistment Validation</a>
                    </div>
                </div>
                <div class="admin-home-hero-panel">
                    <div class="admin-home-hero-stat">
                        <span>Active Enrollees</span>
                        <strong><?php echo (int) $totalEnrolled; ?></strong>
                    </div>
                    <div class="admin-home-hero-stat">
                        <span>Promoted Students</span>
                        <strong><?php echo (int) $promotedCount; ?></strong>
                    </div>
                    <div class="admin-home-hero-stat">
                        <span>Graduated Students</span>
                        <strong><?php echo (int) $graduatedCount; ?></strong>
                    </div>
                </div>
            </section>

            <section class="admin-home-quicklinks">
                <a href="document_compliance_page.php" class="admin-home-quicklink">
                    <strong>Document Compliance</strong>
                    <span>Check missing requirements and student records.</span>
                </a>
                <a href="student_progress_validation_page.php" class="admin-home-quicklink">
                    <strong>Progress Validation</strong>
                    <span>Confirm teacher recommendations and semester outcomes.</span>
                </a>
                <a href="reports_dashboard_page.php" class="admin-home-quicklink">
                    <strong>Detailed Reports</strong>
                    <span>Open filtered breakdowns for enrollment and approvals.</span>
                </a>
            </section>

           <div class="reports-toolbar">
        <form method="GET" id="schoolYearFilterForm">
            <label for="school_year">School Year:</label>
            <select name="school_year" id="school_year" onchange="this.form.submit()">
                <option value="All" <?php echo $selectedSchoolYear === 'All' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($availableSchoolYears as $schoolYear): ?>
                    <option value="<?php echo htmlspecialchars($schoolYear); ?>" <?php echo $selectedSchoolYear === $schoolYear ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($schoolYear); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-filter">Apply</button>
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
    </div>
        </div>
    </div>
</main>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
