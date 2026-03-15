<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../DB_Connection/Connection.php';
require_once __DIR__ . '/admin_access.php';
require_once __DIR__ . '/theme_preferences.php';

$allowedRoles = [ROLE_STUDENT, ROLE_SUPER_ADMIN, ROLE_TEACHER, ROLE_REGISTRAR];
$user = requirePortalAccess($connection, $allowedRoles, '/Enrollment_System_CDONHS-SHS/Website_Files/login.php');
$roleId = (int) $user['role_id'];
$settingsPath = getSettingsPathForRole($roleId);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $settingsPath);
    exit;
}

$postedTheme = $_POST['theme_preference'] ?? null;
if (!is_string($postedTheme) || !in_array($postedTheme, [THEME_LIGHT, THEME_DARK], true)) {
    header('Location: ' . $settingsPath . '?error=invalid_theme');
    exit;
}

saveThemePreference($connection, (int) $user['user_id'], $postedTheme);

header('Location: ' . $settingsPath . '?status=updated');
exit;
