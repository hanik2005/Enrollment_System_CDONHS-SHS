<!-- keep -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

/* Verify student session and get profile image */
$stmt = $connection->prepare("
    SELECT u.*, sa.profile_image
    FROM users u
    INNER JOIN students s ON s.user_id = u.user_id
    INNER JOIN student_applications sa ON s.application_id = sa.application_id
    WHERE u.user_id = ? AND u.role_id = 1
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$profileImagePath = !empty($user['profile_image'])
    ? "../../uploads/Profile/student/" . htmlspecialchars($user['profile_image'])
    : "../../Assets/profile_button.png";

$displayName = trim(($user['first_name'] ?? '') . " " . ($user['last_name'] ?? ''));
if ($displayName === '') {
    $displayName = $user['username'] ?? ($user['email'] ?? "Student User");
}

include "../../Back_End_Files/PHP_Files/get_student_program.php";

$facebookPageUrl = "https://www.facebook.com/CDONHSSrHigh";

if ($isEnlisted) {
    $programText = htmlspecialchars($gradeLevel) . ", " . htmlspecialchars($strandName) . ", " . htmlspecialchars($sectionName);
} elseif ($isPending) {
    $programText = "Pending Enlistment";
} elseif ($isRejected) {
    $programText = "Rejected Enlistment";
} elseif ($Promoted) {
    $programText = "Promoted";
} else {
    $programText = "Not enrolled yet";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <title>Student Home</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>

    <div class="center">
        Program: <?php echo $programText; ?>
    </div>

    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="student-home-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<div id="student-home-menu" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Student navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="<?php echo $profileImagePath; ?>" alt="Student profile">
            <div>
                <h3><?php echo htmlspecialchars($displayName); ?></h3>
                <p>Student</p>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Student page links">
            <a href="profile_page.php">My Profile</a>
            <?php if (!$isEnlisted && !$isPending && !$Promoted): ?>
                <a href="student_enlistment.php">Enlistment</a>
            <?php elseif ($isPending): ?>
                <span class="menu-link-disabled">Pending Enlistment</span>
            <?php else: ?>
                <span class="menu-link-disabled">Already Enlisted</span>
            <?php endif; ?>
            <a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
        </nav>
    </aside>
</div>

<main id="main-content">
    <div class="dashboard">
        <div class="dashboard-box home-dashboard-box">
            <h2 class="home-dashboard-title">Student Dashboard</h2>
            <p class="home-dashboard-subtitle">Use the menu button for navigation. Announcements and school information are shown below.</p>

            <section class="home-hero">
                <div>
                    <span class="home-hero-tag">Official School Updates</span>
                    <h2>Cagayan de Oro National High School - Senior High School</h2>
                    <p>Welcome to the CDONHS-SHS dashboard. Get official announcements, enrollment updates, and important school reminders in one place.</p>
                    <div class="home-hero-actions">
                        <a class="home-facebook-link" href="<?php echo $facebookPageUrl; ?>" target="_blank" rel="noopener noreferrer">View Official Facebook Announcements</a>
                        <a class="home-secondary-link" href="#student-school-info">Explore School Information</a>
                    </div>
                </div>
                <img class="home-hero-image" src="../../Assets/dashboard_samples/school_campus_aerial_web.png" alt="CDONHS-SHS campus">
            </section>

            <div class="home-info-grid" id="student-school-info">
                <section class="home-info-card">
                    <h3>Announcements</h3>
                    <p>Latest advisories, enrollment notices, and school events are posted on the official Facebook page.</p>
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
                    <h3>Current Program Status</h3>
                    <p><?php echo $programText; ?></p>
                </section>
            </div>

            <section class="home-gallery" aria-label="Featured school photos">
                <h3>Featured School Photos</h3>
                <div class="home-gallery-grid">
                    <img src="../../Assets/dashboard_samples/classroom_students_1_web.jpg" alt="Students in classroom activity">
                    <img src="../../Assets/dashboard_samples/classroom_students_2_web.jpg" alt="Students learning with classmates">
                    <img src="../../Assets/dashboard_samples/students_group_1_web.jpg" alt="Group of students smiling">
                </div>
            </section>

            <div class="home-info-grid">
                <section class="home-info-card">
                    <h3>Quick Reminder</h3>
                    <p>Always check your enlistment status and school announcements before important enrollment dates.</p>
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
