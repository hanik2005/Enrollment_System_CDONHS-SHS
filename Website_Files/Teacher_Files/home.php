<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

$stmt = $connection->prepare("
    SELECT u.*, t.teacher_id, t.first_name, t.last_name, t.middle_name, t.extension_name
    FROM users u
    INNER JOIN teachers t ON t.user_id = u.user_id
    WHERE u.user_id = ? AND u.role_id = 3
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

$profileImagePath = "../../Assets/profile_button.png";

$nameParts = [];
foreach (['first_name', 'middle_name', 'last_name', 'extension_name'] as $field) {
    if (!empty($user[$field])) {
        $nameParts[] = $user[$field];
    }
}
$displayName = trim(implode(" ", $nameParts));
if ($displayName === '') {
    $displayName = $user['username'] ?? ($user['email'] ?? "Teacher User");
}

$facebookPageUrl = "https://www.facebook.com/CDONHSSrHigh";

include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";

$teacherSummary = [
    'total_students' => 0,
    'enlisted' => 0,
    'pending' => 0,
    'missing_docs' => 0,
    'pending_validation' => 0,
];

if (!empty($advisorySectionId)) {
    $summaryStmt = $connection->prepare("
        SELECT
            s.student_id,
            s.enrollment_status,
            s.enlistment_status,
            COALESCE(sd.psa_birth_certificate, '') AS psa_birth_certificate,
            COALESCE(sd.form_138, '') AS form_138,
            COALESCE(sd.student_id_copy, '') AS student_id_copy
        FROM students s
        INNER JOIN student_strand ss ON ss.student_id = s.student_id
        INNER JOIN student_applications sa ON sa.application_id = s.application_id
        LEFT JOIN student_documents sd ON sd.application_id = sa.application_id
        WHERE ss.section_id = ?
          AND COALESCE(s.enrollment_status, '') <> 'Graduated'
          AND COALESCE(s.enlistment_status, '') <> 'Finished'
    ");
    $summaryStmt->bind_param("i", $advisorySectionId);
    $summaryStmt->execute();
    $summaryRows = $summaryStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $summaryStmt->close();

    $uniqueStudents = [];
    foreach ($summaryRows as $row) {
        $uniqueStudents[(int) $row['student_id']] = $row;
    }

    foreach ($uniqueStudents as $row) {
        $teacherSummary['total_students']++;

        if (strcasecmp((string) ($row['enlistment_status'] ?? ''), 'Enlisted') === 0) {
            $teacherSummary['enlisted']++;
        }

        if (strcasecmp((string) ($row['enlistment_status'] ?? ''), 'Pending') === 0) {
            $teacherSummary['pending']++;
        }

        if (
            trim((string) $row['psa_birth_certificate']) === '' ||
            trim((string) $row['form_138']) === '' ||
            trim((string) $row['student_id_copy']) === ''
        ) {
            $teacherSummary['missing_docs']++;
        }
    }
}

$validationStmt = $connection->prepare("
    SELECT COUNT(DISTINCT student_id) AS total
    FROM student_promotion_status
    WHERE teacher_id = ? AND approval_status = 'Pending'
");
if ($validationStmt) {
    $validationStmt->bind_param("i", $user['teacher_id']);
    $validationStmt->execute();
    $teacherSummary['pending_validation'] = (int) (($validationStmt->get_result()->fetch_assoc()['total'] ?? 0));
    $validationStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/teacher/teacher_home_design.css">
    <title>Teacher Home</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONHS-SHS</span>
    </div>

    <?php echo renderPortalHeaderBanner('Teacher Portal', 'Teacher', 'Advisory: ' . $advisoryText); ?>

    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="teacher-home-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<div id="teacher-home-menu" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Teacher navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="<?php echo $profileImagePath; ?>" alt="Teacher profile">
            <div>
                <h3><?php echo htmlspecialchars($displayName); ?></h3>
                <p>Advisory: <?php echo htmlspecialchars($advisoryText); ?></p>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Teacher page links">
            <a href="class_list.php">Class List</a>
            <a href="student_progress_page.php">Student Progress</a>
            <a href="enrollment_summary_page.php">Enrollment Summary</a>
            <a href="teacher_advisory_notes_page.php">Advisory Notes</a>
            <a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
        </nav>
    </aside>
</div>

<main id="main-content">
    <div class="dashboard">
        <div class="dashboard-box home-dashboard-box">
            <h2 class="home-dashboard-title">Teacher Dashboard</h2>
            <p class="home-dashboard-subtitle">Quick access to the most important teacher pages, plus live advisory counts for your section.</p>

            <section class="teacher-home-priority">
                <div class="teacher-home-priority-copy">
                    <span class="teacher-home-badge">Teacher Workspace</span>
                    <h2>Go straight to the teacher pages you use every day</h2>
                    <p>Open your class list, record student progress, review enrollment summary, and keep advisory notes updated without digging through the menu first.</p>
                    <div class="teacher-home-priority-actions">
                        <a href="class_list.php" class="teacher-home-primary-link">Open Class List</a>
                        <a href="student_progress_page.php" class="home-secondary-link">Open Student Progress</a>
                    </div>
                </div>
                <div class="teacher-home-priority-stats">
                    <div class="teacher-home-priority-card">
                        <span>Total Advisory Students</span>
                        <strong><?php echo (int) $teacherSummary['total_students']; ?></strong>
                    </div>
                    <div class="teacher-home-priority-card">
                        <span>Pending Validation</span>
                        <strong><?php echo (int) $teacherSummary['pending_validation']; ?></strong>
                    </div>
                </div>
            </section>

            <section class="teacher-home-nav-grid">
                <a href="class_list.php" class="teacher-home-nav-card">
                    <strong>Class List</strong>
                    <span>View the students currently under your advisory section.</span>
                </a>
                <a href="student_progress_page.php" class="teacher-home-nav-card">
                    <strong>Student Progress</strong>
                    <span>Encode recommendations and monitor approval workflow.</span>
                </a>
                <a href="enrollment_summary_page.php" class="teacher-home-nav-card">
                    <strong>Enrollment Summary</strong>
                    <span>Check enlistment and missing document status at a glance.</span>
                </a>
                <a href="teacher_advisory_notes_page.php" class="teacher-home-nav-card">
                    <strong>Advisory Notes</strong>
                    <span>Capture student observations, follow-ups, and interventions.</span>
                </a>
            </section>

            <section class="teacher-home-report-grid">
                <div class="teacher-home-report-card">
                    <span>Total Students</span>
                    <strong><?php echo (int) $teacherSummary['total_students']; ?></strong>
                </div>
                <div class="teacher-home-report-card">
                    <span>Enlisted</span>
                    <strong><?php echo (int) $teacherSummary['enlisted']; ?></strong>
                </div>
                <div class="teacher-home-report-card">
                    <span>Pending Enlistment</span>
                    <strong><?php echo (int) $teacherSummary['pending']; ?></strong>
                </div>
                <div class="teacher-home-report-card">
                    <span>Missing Documents</span>
                    <strong><?php echo (int) $teacherSummary['missing_docs']; ?></strong>
                </div>
            </section>

            <section class="home-hero">
                <div>
                    <span class="home-hero-tag">Official School Updates</span>
                    <h2>Cagayan de Oro National High School - Senior High School</h2>
                    <p>Welcome, Teacher. Stay updated with school announcements, advisory reminders, and key academic updates from the official channel.</p>
                    <div class="home-hero-actions">
                        <a class="home-facebook-link" href="<?php echo $facebookPageUrl; ?>" target="_blank" rel="noopener noreferrer">View Official Facebook Announcements</a>
                        <a class="home-secondary-link" href="#teacher-school-info">Explore School Information</a>
                    </div>
                </div>
                <img class="home-hero-image" src="../../Assets/dashboard_samples/school_campus_aerial_web.png" alt="CDONHS-SHS campus">
            </section>

            <div class="home-info-grid" id="teacher-school-info">
                <section class="home-info-card">
                    <h3>Announcements</h3>
                    <p>Check enrollment, calendar, and activity advisories through the official school Facebook page.</p>
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
                    <h3>Advisory Assignment</h3>
                    <p><?php echo htmlspecialchars($advisoryText); ?></p>
                </section>
            </div>

            <section class="home-gallery" aria-label="Featured school photos">
                <h3>Featured School Photos</h3>
                <div class="home-gallery-grid">
                    <img src="../../Assets/dashboard_samples/classroom_students_2_web.jpg" alt="Classroom learning session">
                    <img src="../../Assets/dashboard_samples/classroom_students_3_web.jpg" alt="Students studying together">
                    <img src="../../Assets/dashboard_samples/students_group_1_web.jpg" alt="Student group activity">
                </div>
            </section>

            <div class="home-info-grid">
                <section class="home-info-card">
                    <h3>Teaching Reminder</h3>
                    <p>Review advisory notes, progress records, and enrollment summaries regularly to keep class data updated.</p>
                </section>
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
