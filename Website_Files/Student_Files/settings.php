<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../DB_Connection/Connection.php";
require_once "../../Back_End_Files/PHP_Files/portal_ui_helper.php";

$stmt = $connection->prepare("
    SELECT u.username, sa.profile_image, sa.first_name, sa.last_name, sa.middle_name, sa.extension_name, sa.lrn
    FROM users u
    INNER JOIN students s ON s.user_id = u.user_id
    INNER JOIN student_applications sa ON sa.application_id = s.application_id
    WHERE u.user_id = ? AND u.role_id = 1
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

syncSessionThemePreference($connection, (int) $_SESSION['user_id']);
require "../../Back_End_Files/PHP_Files/get_student_program.php";

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

$currentTheme = getCurrentThemePreference();
$statusMessage = '';
$statusClass = '';
if (($_GET['status'] ?? '') === 'updated') {
    $statusMessage = 'Theme preference saved to your student account.';
    $statusClass = 'settings-alert-success';
} elseif (($_GET['error'] ?? '') === 'invalid_theme') {
    $statusMessage = 'Please choose a valid theme option.';
    $statusClass = 'settings-alert-error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Settings</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/portal_settings.css">
</head>
<body <?php echo renderThemeBodyAttributes('student-settings-page'); ?>>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <?php echo renderPortalHeaderBanner('Student Portal', 'Settings', 'Appearance preferences'); ?>
    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="student-settings-menu">
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
    'student-settings-menu',
    $profileImagePath,
    $displayName,
    (string) ($user['lrn'] ?? ''),
    $gradeLevel !== null ? (string) $gradeLevel : null,
    $strandName,
    $sectionName,
    $studentMenuLinks
); ?>

<main id="main-content" class="settings-page">
    <div class="settings-shell">
        <section class="settings-hero">
            <div>
                <span class="settings-kicker">Appearance Settings</span>
                <h1>Choose the portal theme for your student account</h1>
                <p>Switch between light mode and dark mode for your own login. Your choice stays with your account only and does not change other students' screens.</p>
            </div>
            <div class="settings-theme-state">
                <span>Current theme</span>
                <strong><?php echo htmlspecialchars(getThemeLabel($currentTheme)); ?></strong>
            </div>
        </section>

        <?php if ($statusMessage !== ''): ?>
            <div class="settings-alert <?php echo $statusClass; ?>">
                <?php echo htmlspecialchars($statusMessage); ?>
            </div>
        <?php endif; ?>

        <section class="settings-card">
            <div class="settings-card-head">
                <h2>Theme Preference</h2>
                <p>Update how your student home page, profile page, and enlistment page look whenever you sign in.</p>
            </div>

            <form class="settings-form" method="POST" action="../../Back_End_Files/PHP_Files/theme_settings_backend.php">
                <div class="settings-theme-grid">
                    <label class="settings-theme-option<?php echo $currentTheme === THEME_LIGHT ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="light" <?php echo $currentTheme === THEME_LIGHT ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-light"></div>
                        <strong>Light Mode</strong>
                        <p>Bright cards and the standard school colors for daytime browsing.</p>
                    </label>

                    <label class="settings-theme-option<?php echo $currentTheme === THEME_DARK ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="dark" <?php echo $currentTheme === THEME_DARK ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-dark"></div>
                        <strong>Dark Mode</strong>
                        <p>Deeper backgrounds and softer glare for night study or long reading sessions.</p>
                    </label>
                </div>

                <div class="settings-actions">
                    <button type="submit" class="settings-save-btn">Save Theme Preference</button>
                    <a href="home.php" class="back-button">Back to Home</a>
                </div>
            </form>
        </section>
    </div>
</main>

<div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School
</div>
<script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
