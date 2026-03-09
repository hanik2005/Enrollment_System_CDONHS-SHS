<!-- keep -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

/* Verify student session and get profile image */
$stmt = $connection->prepare("
    SELECT u.*, sa.profile_image, sa.first_name, sa.last_name, sa.middle_name, sa.extension_name, sa.lrn
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

$displayName = formatPortalPersonName(
    $user['first_name'] ?? null,
    $user['middle_name'] ?? null,
    $user['last_name'] ?? null,
    $user['extension_name'] ?? null,
    $user['username'] ?? ($user['email'] ?? 'Student User')
);

include "../../Back_End_Files/PHP_Files/get_student_program.php";

$facebookPageUrl = "https://www.facebook.com/CDONHSSrHigh";

if ($isEnlisted) {
    $programText = htmlspecialchars($gradeLevel) . ", " . htmlspecialchars($strandName) . ", " . htmlspecialchars($sectionName);
} elseif ($isPending) {
    $programText = "Pending Enlistment";
} elseif ($isRejected) {
    $programText = "Rejected Enlistment";
} elseif ($isGraduated) {
    $programText = "Already graduated and cannot be enlisted again";
} elseif ($Promoted) {
    $programText = "Promoted";
} else {
    $programText = "Not enrolled yet";
}

$studentMenuLinks = '<a href="profile_page.php">My Profile</a>';
if (!$isEnlisted && !$isPending && !$Promoted && !$isGraduated) {
    $studentMenuLinks .= '<a href="student_enlistment.php">Enlistment</a>';
} elseif ($isPending) {
    $studentMenuLinks .= '<span class="menu-link-disabled">Pending Enlistment</span>';
} elseif ($isGraduated) {
    $studentMenuLinks .= '<span class="menu-link-disabled">Already Graduated</span>';
} else {
    $studentMenuLinks .= '<span class="menu-link-disabled">Already Enlisted</span>';
}
$studentMenuLinks .= '<a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>';
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
    <link rel="stylesheet" href="../../Design/student/student_home_design.css">
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

    <?php echo renderPortalHeaderBanner('Student Portal', 'Student', 'Program: ' . $programText); ?>

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

<?php echo renderStudentMenuOverlay(
    'student-home-menu',
    $profileImagePath,
    $displayName,
    (string) ($user['lrn'] ?? ''),
    $gradeLevel !== null ? (string) $gradeLevel : null,
    $strandName,
    $sectionName,
    $studentMenuLinks
); ?>

<main id="main-content">

    <section class="home-section hero-section">

        <section class="home-hero">
        <div>
        <span class="home-hero-tag">Official School Updates</span>

        <h2>Cagayan de Oro National High School - Senior High School</h2>

        <p>
        Welcome to the CDONHS-SHS dashboard. Get official announcements,
        enrollment updates, and important school reminders in one place.
        </p>

        <div class="home-hero-actions">
        <a class="home-facebook-link"
        href="<?php echo $facebookPageUrl; ?>"
        target="_blank"
        rel="noopener noreferrer">
        View Official Facebook Announcements
        </a>

        <?php if (!$isEnlisted && !$isPending && !$Promoted && !$isGraduated): ?>
         <a class="home-enlistment-link" 
         href="student_enlistment.php">Click Enlistment to have Strand and Section
        </a>
        <?php endif; ?>

        </div>
        </div>

        <img class="home-hero-image"
        src="../../Assets/dashboard_samples/school_campus_aerial_web.png"
        alt="CDONHS-SHS campus">
        </section>

    </section>


    <section class="home-section info-section" id="student-school-info">

        <div class="home-info-grid">

        <section class="home-info-card">
            <h3>Announcements</h3>
            <p>
            Latest advisories, enrollment notices, and school events are
            posted on the official Facebook page.
            </p>

            <a class="home-facebook-link"
            href="<?php echo $facebookPageUrl; ?>"
            target="_blank"
            rel="noopener noreferrer">
            Open Facebook Updates
            </a>
        </section>

        <section class="home-info-card">
            <h3>Mission</h3>
            <p>
            To protect and promote the right of every Filipino to quality,
            equitable, culture-based, and complete basic education in a safe
            and motivating environment.
            </p>
        </section>

        <section class="home-info-card">
            <h3>Vision</h3>
            <p>
            We dream of Filipinos who passionately love their country and
            whose values and competencies enable them to realize their full
            potential and contribute to nation-building.
            </p>
            </section>

            <section class="home-info-card">
            <h3>Current Program Status</h3>
            <p><?php echo $programText; ?></p>
            </section>

            </div>

        </section>


        <section class="home-section gallery-section">

            <section class="home-gallery">

                <h3>Featured School Photos</h3>

                <div class="home-gallery-grid">

                <img src="../../Assets/dashboard_samples/classroom_students_1_web.jpg"
                alt="Students in classroom activity">

                <img src="../../Assets/dashboard_samples/classroom_students_2_web.jpg"
                alt="Students learning with classmates">

                <img src="../../Assets/dashboard_samples/students_group_1_web.jpg"
                alt="Group of students smiling">

                </div>

            </section>

            <section class="home-info-card reminder-card">

            <h3>Quick Reminder</h3>

            <p>
            Always check your enlistment status and school announcements
            before important enrollment dates.
            </p>

        </section>

    </section>

</main>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
