<?php
include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
$currentTheme = getCurrentThemePreference();
$displayName = $admin['username'] ?? ($admin['email'] ?? 'Admin User');

$statusMessage = '';
$statusClass = '';
if (($_GET['status'] ?? '') === 'updated') {
    $statusMessage = 'Theme preference saved to your account.';
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
    <title>Admin Settings</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/portal_settings.css">
</head>
<body <?php echo renderThemeBodyAttributes('admin-settings-page'); ?>>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Settings'); ?>
    <div class="right">
        <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="admin-settings-menu">
            <span class="menu-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <span class="menu-label">Menu</span>
        </button>
    </div>
</div>

<div id="admin-settings-menu" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Admin navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="../../Assets/admin_profile.png" alt="Admin profile">
            <div class="home-menu-profile-copy">
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

<main id="main-content" class="settings-page">
    <div class="settings-shell">
        <section class="settings-hero">
            <div>
                <span class="settings-kicker">Appearance Settings</span>
                <h1>Choose the portal theme for your admin account</h1>
                <p>Pick light mode or dark mode for this login. The preference is saved only to your account, so other users keep their own theme choice.</p>
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
                <p>Change how the admin dashboard, validation pages, and reports look when you are signed in.</p>
            </div>

            <form class="settings-form" method="POST" action="../../Back_End_Files/PHP_Files/theme_settings_backend.php">
                <div class="settings-theme-grid">
                    <label class="settings-theme-option<?php echo $currentTheme === THEME_LIGHT ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="light" <?php echo $currentTheme === THEME_LIGHT ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-light"></div>
                        <strong>Light Mode</strong>
                        <p>Bright cards, classic blue-and-gold contrast, and daytime readability.</p>
                    </label>

                    <label class="settings-theme-option<?php echo $currentTheme === THEME_DARK ? ' is-selected' : ''; ?>">
                        <input type="radio" name="theme_preference" value="dark" <?php echo $currentTheme === THEME_DARK ? 'checked' : ''; ?>>
                        <div class="settings-theme-preview settings-theme-preview-dark"></div>
                        <strong>Dark Mode</strong>
                        <p>Low-glare surfaces with stronger contrast for longer admin work sessions.</p>
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
