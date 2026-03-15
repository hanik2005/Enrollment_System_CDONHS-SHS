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
    SELECT u.username, t.teacher_id, t.first_name, t.last_name, t.middle_name, t.extension_name
    FROM users u
    INNER JOIN teachers t ON t.user_id = u.user_id
    WHERE u.user_id = ? AND u.role_id = 3
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
include "../../Back_End_Files/PHP_Files/get_teacher_advisory.php";

$profileImagePath = "../../Assets/profile_button.png";
$displayName = formatPortalPersonName(
    $user['first_name'] ?? null,
    $user['middle_name'] ?? null,
    $user['last_name'] ?? null,
    $user['extension_name'] ?? null,
    $user['username'] ?? 'Teacher User'
);

$currentTheme = getCurrentThemePreference();
$statusMessage = '';
$statusClass = '';
if (($_GET['status'] ?? '') === 'updated') {
    $statusMessage = 'Theme preference saved to your teacher account.';
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
    <title>Teacher Settings</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/portal_settings.css">
</head>
<body <?php echo renderThemeBodyAttributes('teacher-settings-page'); ?>>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <?php echo renderPortalHeaderBanner('Teacher Portal', 'Settings', 'Advisory: ' . $advisoryText); ?>
    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="teacher-settings-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<div id="teacher-settings-menu" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Teacher navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="<?php echo $profileImagePath; ?>" alt="Teacher profile">
            <div class="home-menu-profile-copy">
                <h3><?php echo htmlspecialchars($displayName); ?></h3>
                <p>Advisory: <?php echo htmlspecialchars($advisoryText); ?></p>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Teacher page links">
            <a href="home.php">Home</a>
            <a href="class_list.php">Class List</a>
            <a href="enrollment_summary_page.php">Enrollment Summary</a>
            <a href="teacher_advisory_notes_page.php">Advisory Notes</a>
            <a href="settings.php">Settings</a>
            <a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
        </nav>
    </aside>
</div>

<main id="main-content" class="settings-page">
    <div class="settings-shell">
        <section class="settings-hero">
            <div>
                <span class="settings-kicker">Appearance Settings</span>
                <h1>Choose the portal theme for your teacher account</h1>
                <p>Switch between light mode and dark mode for your own teacher workspace. The saved theme only follows this login and does not affect other teachers.</p>
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
                <p>Change how the teacher dashboard, class list, enrollment summary, and advisory notes pages appear when you sign in.</p>
            </div>

            <form class="settings-form" method="POST" action="../../Back_End_Files/PHP_Files/theme_settings_backend.php">
                <div class="settings-theme-grid">
                    <label class="settings-theme-option<?php echo $currentTheme === THEME_LIGHT ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="light" <?php echo $currentTheme === THEME_LIGHT ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-light"></div>
                        <strong>Light Mode</strong>
                        <p>Standard bright portal surfaces and familiar daytime contrast.</p>
                    </label>

                    <label class="settings-theme-option<?php echo $currentTheme === THEME_DARK ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="dark" <?php echo $currentTheme === THEME_DARK ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-dark"></div>
                        <strong>Dark Mode</strong>
                        <p>Darker cards and lower glare for longer advisory and reporting sessions.</p>
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
