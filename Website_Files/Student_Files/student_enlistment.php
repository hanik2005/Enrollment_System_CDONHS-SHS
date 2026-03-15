<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";
include "../../Back_End_Files/PHP_Files/get_student_program.php";

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

$stmtEnrollment = $connection->prepare("
    SELECT enrollment_status
    FROM students
    WHERE user_id = ?
    LIMIT 1
");
$stmtEnrollment->bind_param("i", $_SESSION['user_id']);
$stmtEnrollment->execute();
$enrollmentResult = $stmtEnrollment->get_result();
$studentStatus = $enrollmentResult->fetch_assoc();
$stmtEnrollment->close();

if ($studentStatus && $studentStatus['enrollment_status'] === 'Graduated') {
    header("Location: home.php");
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
    $user['username'] ?? 'Student User'
);

$studentMenuLinks = '<a href="home.php">Home</a>';
$studentMenuLinks .= '<a href="profile_page.php">My Profile</a>';
$studentMenuLinks .= '<a href="settings.php">Settings</a>';
$studentMenuLinks .= '<a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>';

$now = new DateTime('now', new DateTimeZone('Asia/Manila'));
$currentMonth = (int)$now->format('n');
$currentYear = (int)$now->format('Y');

if ($currentMonth >= 8) {
    $currentSemester = '1st Semester';
    $currentSchoolYear = $currentYear . '-' . ($currentYear + 1);
} else {
    $currentSemester = '2nd Semester';
    $currentSchoolYear = ($currentYear - 1) . '-' . $currentYear;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/student/enlistment.css">
    <title>Student Enlistment</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>

    <?php echo renderPortalHeaderBanner('Student Portal', 'Enlistment', $currentSemester . ' | ' . $currentSchoolYear); ?>

    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="student-enlistment-menu">
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
    'student-enlistment-menu',
    $profileImagePath,
    $displayName,
    (string) ($user['lrn'] ?? ''),
    $gradeLevel !== null ? (string) $gradeLevel : null,
    $strandName,
    $sectionName,
    $studentMenuLinks
); ?>

<div class="enlistment-container">
    <div class="enlistment-hero">
        <h2>Student Enlistment</h2>
        <p>Review your current term, then select your grade level, track, strand, and section.</p>
        <div class="term-badges">
            <span class="term-badge">Semester: <?php echo htmlspecialchars($currentSemester); ?></span>
            <span class="term-badge">School Year: <?php echo htmlspecialchars($currentSchoolYear); ?></span>
        </div>
    </div>

    <form id="enlistment-form">
        <div class="enlistment-content">
            <div class="left-panel panel-card">
                <h3>Choose Placement</h3>

                <label for="grade_level">Grade Level</label>
                <select id="grade_level" name="grade_level" required>
                    <option value="">Select Grade Level</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>

                <label for="track">Track</label>
                <select id="track" name="track" required>
                    <option value="">Select Track</option>
                </select>

                <label for="strand">Strand</label>
                <select id="strand" name="strand" required>
                    <option value="">Select Track First</option>
                </select>

                <label for="section">Section</label>
                <select id="section" name="section" required>
                    <option value="">Select Section</option>
                </select>
            </div>

            <div class="right-panel panel-card">
                <h3>Selected Enlistment Details</h3>
                <div id="selection-summary">
                    <p>Please select your grade level, track, strand, and section.</p>
                    <ul>
                        <li><strong>Semester:</strong> <span id="summary-semester"><?php echo htmlspecialchars($currentSemester); ?></span></li>
                        <li><strong>School Year:</strong> <span id="summary-school-year"><?php echo htmlspecialchars($currentSchoolYear); ?></span></li>
                        <li><strong>Grade Level:</strong> <span id="summary-grade">Not selected</span></li>
                        <li><strong>Track:</strong> <span id="summary-track">Not selected</span></li>
                        <li><strong>Strand:</strong> <span id="summary-strand">Not selected</span></li>
                        <li><strong>Section:</strong> <span id="summary-section">Not selected</span></li>
                    </ul>
                </div>

                <button type="submit" class="submit-btn">Submit Enlistment</button>
            </div>
        </div>
    </form>
</div>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>

<script src="../../Back_End_Files/JSCRIPT_Files/enlistment_get_boxes.js?v=<?php echo time(); ?>"></script>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
