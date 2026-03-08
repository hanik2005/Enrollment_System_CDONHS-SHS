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
<body>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>

    <div class="right">
        <button class="legacy-menu-trigger" type="button">
            <img src="<?php echo $profileImagePath; ?>" alt="Profile">
        </button>

        <div class="legacy-nav-links">
            <a href="profile_page.php">View Profile</a>
            <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
        </div>
    </div>
</div>

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
    <br>
    School Management System
</div>

<script src="../../Back_End_Files/JSCRIPT_Files/enlistment_get_boxes.js?v=<?php echo time(); ?>"></script>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
