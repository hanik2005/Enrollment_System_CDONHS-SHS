<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($connection, "
    SELECT * FROM users
    WHERE user_id = ?
    AND role_id = 2
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$profileImagePath = "../../Assets/admin_profile.png";
$displayName = $admin['username'] ?? ($admin['email'] ?? ($admin['first_name'] ?? "Admin User"));
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
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <div class="center">
        Admin
    </div>
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
                <p>Administrator</p>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Admin page links">
            <a href="admin_student_application_list.php">Application List</a>
            <a href="sensitive_information.php">Sensitive Information</a>
            <a href="activation_page.php">Activation Page</a>
            <a href="enlistment_validation_page.php">Enlistment Validation</a>
            <a href="teacher_advisory_page.php">Teacher Advisory</a>
            <a href="document_compliance_page.php">Document Compliance</a>
            <a href="reports_dashboard_page.php">Reports Dashboard</a>
            <a href="document_correction_page.php">Document Correction</a>
            <a href="audit_trail_page.php">Audit Trail</a>
            <a href="student_progress_validation_page.php">Student Progress Validation</a>
            <a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
        </nav>
    </aside>
</div>

<main id="main-content">
    <div class="dashboard">
        <div class="dashboard-box home-dashboard-box">
            <h2 class="home-dashboard-title">Admin Dashboard</h2>
            <p class="home-dashboard-subtitle">Primary admin navigation is in the menu button. This dashboard focuses on official updates and school direction.</p>

            <section class="home-hero">
                <div>
                    <span class="home-hero-tag">Official School Updates</span>
                    <h2>Cagayan de Oro National High School - Senior High School</h2>
                    <p>Welcome to the admin dashboard. Monitor official announcements, enrollment operations, and school-level updates in one place.</p>
                    <div class="home-hero-actions">
                        <a class="home-facebook-link" href="<?php echo $facebookPageUrl; ?>" target="_blank" rel="noopener noreferrer">View Official Facebook Announcements</a>
                        <a class="home-secondary-link" href="#admin-school-info">Explore School Information</a>
                    </div>
                </div>
                <img class="home-hero-image" src="../../Assets/dashboard_samples/school_campus_aerial_web.png" alt="CDONHS-SHS campus">
            </section>

            <div class="home-info-grid" id="admin-school-info">
                <section class="home-info-card">
                    <h3>Announcements</h3>
                    <p>Publish and monitor key school updates through the official Facebook page and internal admin channels.</p>
                    <a class="home-facebook-link" href="<?php echo $facebookPageUrl; ?>" target="_blank" rel="noopener noreferrer">Open Facebook Updates</a>
                </section>

                <section class="home-info-card">
                    <h3>Mission</h3>
                    <p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education in a safe and motivating environment.</p>
                </section>

                <section class="home-info-card">
                    <h3>Vision</h3>
                    <p>We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute to nation-building.</p>
                </section>

                <section class="home-info-card">
                    <h3>Admin Focus</h3>
                    <p>Keep application validation, compliance checks, progress validation, and reporting workflows aligned with official schedules.</p>
                </section>
            </div>

            <section class="home-gallery" aria-label="Featured school photos">
                <h3>Featured School Photos</h3>
                <div class="home-gallery-grid">
                    <img src="../../Assets/dashboard_samples/classroom_students_1_web.jpg" alt="Students during classroom discussion">
                    <img src="../../Assets/dashboard_samples/school_campus_aerial_web.png" alt="Campus aerial view">
                    <img src="../../Assets/dashboard_samples/students_group_1_web.jpg" alt="Students in group photo">
                </div>
            </section>

            <div class="home-info-grid">
                <section class="home-info-card">
                    <h3>Admin Reminder</h3>
                    <p>Verify dashboard reports and pending validations daily to keep student records and advisories up to date.</p>
                </section>
            </div>
        </div>
    </div>
</main>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
    <br>
    School Management System
</div>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
