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

syncSessionThemePreference($connection, (int) $_SESSION['user_id']);

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
$studentMenuLinks .= '<a href="settings.php">Settings</a>';
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
<body <?php echo renderThemeBodyAttributes('student-home-page'); ?>>
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
            <div class="home-hero-content">
                <span class="home-hero-tag">Official School Updates</span>
                <h2>Cagayan de Oro National High School - Senior High School</h2>
                <p>
                    Welcome to the CDONHS-SHS student dashboard. Check official announcements,
                    monitor your current school standing, and keep up with important enrollment
                    reminders from one place.
                </p>

                <div class="home-hero-status-row">
                    <div class="home-status-chip">
                        <span>Current Standing</span>
                        <strong><?php echo htmlspecialchars($programText); ?></strong>
                    </div>
                    <div class="home-status-chip">
                        <span>Student LRN</span>
                        <strong><?php echo htmlspecialchars($user['lrn'] ?? 'Not available'); ?></strong>
                    </div>
                </div>

                <div class="home-hero-actions">
                    <a class="home-facebook-link"
                       href="<?php echo $facebookPageUrl; ?>"
                       target="_blank"
                       rel="noopener noreferrer">
                        View Official Facebook Announcements
                    </a>

                    <?php if (!$isEnlisted && !$isPending && !$Promoted && !$isGraduated): ?>
                        <a class="home-enlistment-link" href="student_enlistment.php">
                            Open Enlistment and complete your strand and section request
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="home-hero-visual">
                <img class="home-hero-image"
                     src="../../Assets/dashboard_samples/school_campus_aerial_web.png"
                     alt="CDONHS-SHS campus">

                <div class="home-hero-panel">
                    <span class="home-hero-panel-tag">Student Snapshot</span>
                    <strong><?php echo htmlspecialchars($displayName); ?></strong>
                    <p>Keep your profile complete, watch your enlistment status, and stay connected to verified school updates.</p>

                    <div class="home-hero-panel-grid">
                        <div class="home-hero-panel-item">
                            <span>Portal Access</span>
                            <strong>Active</strong>
                        </div>
                        <div class="home-hero-panel-item">
                            <span>Grade Level</span>
                            <strong><?php echo $gradeLevel !== null ? 'Grade ' . htmlspecialchars((string) $gradeLevel) : 'Pending'; ?></strong>
                        </div>
                        <div class="home-hero-panel-item">
                            <span>Strand</span>
                            <strong><?php echo htmlspecialchars($strandName ?: 'Not assigned'); ?></strong>
                        </div>
                        <div class="home-hero-panel-item">
                            <span>Section</span>
                            <strong><?php echo htmlspecialchars($sectionName ?: 'Not assigned'); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>


    <section class="home-section info-section" id="student-school-info">
        <div class="home-section-heading">
            <span class="home-section-kicker">School Essentials</span>
            <h3 class="home-section-title">Everything you need in one student dashboard</h3>
            <p class="home-section-copy">
                Follow official notices, review the school mission and vision, and keep an eye on your current
                academic standing without leaving the portal.
            </p>
        </div>

        <div class="home-info-grid">

        <section class="home-info-card home-info-card-announcements">
            <span class="home-card-tag">Updates</span>
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

        <section class="home-info-card home-info-card-mission">
            <span class="home-card-tag">Mission</span>
            <h3>Mission</h3>
            <p>
            To protect and promote the right of every Filipino to quality,
            equitable, culture-based, and complete basic education in a safe
            and motivating environment.
            </p>
        </section>

        <section class="home-info-card home-info-card-vision">
            <span class="home-card-tag">Vision</span>
            <h3>Vision</h3>
            <p>
            We dream of Filipinos who passionately love their country and
            whose values and competencies enable them to realize their full
            potential and contribute to nation-building.
            </p>
        </section>

            <section class="home-info-card home-info-card-status">
            <span class="home-card-tag">My Status</span>
            <h3>Current Program Status</h3>
            <p><?php echo $programText; ?></p>
            <div class="home-status-list">
                <div class="home-status-list-item">
                    <span>Grade</span>
                    <strong><?php echo $gradeLevel !== null ? 'Grade ' . htmlspecialchars((string) $gradeLevel) : 'Pending'; ?></strong>
                </div>
                <div class="home-status-list-item">
                    <span>Strand</span>
                    <strong><?php echo htmlspecialchars($strandName ?: 'Not assigned'); ?></strong>
                </div>
                <div class="home-status-list-item">
                    <span>Section</span>
                    <strong><?php echo htmlspecialchars($sectionName ?: 'Not assigned'); ?></strong>
                </div>
            </div>
            </section>

            </div>

        </section>


        <section class="home-section gallery-section">
            <div class="home-section-heading home-section-heading-center">
                <span class="home-section-kicker">Campus Life</span>
                <h3 class="home-section-title">A quick look at the school environment</h3>
                <p class="home-section-copy">
                    Explore featured moments from the campus community and stay mindful of the reminders that keep
                    your enrollment progress on track.
                </p>
            </div>

            <section class="home-gallery">
                <div class="home-gallery-grid">
                    <figure class="home-gallery-item">
                        <img src="../../Assets/dashboard_samples/classroom_students_1_web.jpg"
                             alt="Students in classroom activity">
                        <figcaption>Collaborative classroom activities that support active learning.</figcaption>
                    </figure>

                    <figure class="home-gallery-item">
                        <img src="../../Assets/dashboard_samples/classroom_students_2_web.jpg"
                             alt="Students learning with classmates">
                        <figcaption>Daily study spaces where students learn and work together.</figcaption>
                    </figure>

                    <figure class="home-gallery-item">
                        <img src="../../Assets/dashboard_samples/students_group_1_web.jpg"
                             alt="Group of students smiling">
                        <figcaption>A student community built on support, growth, and school pride.</figcaption>
                    </figure>
                </div>
            </section>

            <section class="home-info-card reminder-card">
                <span class="home-card-tag">Quick Reminder</span>
                <h3>Stay ready for important enrollment dates</h3>
                <p>
                    Always check your enlistment status and official school announcements before deadlines,
                    schedule changes, and major enrollment milestones.
                </p>
                <a class="home-facebook-link"
                   href="<?php echo $facebookPageUrl; ?>"
                   target="_blank"
                   rel="noopener noreferrer">
                    Review official updates
                </a>
            </section>
    </section>

</main>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
