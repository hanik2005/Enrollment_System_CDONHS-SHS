<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/theme_preferences.php';

const ROLE_STUDENT = 1;
const ROLE_SUPER_ADMIN = 2;
const ROLE_TEACHER = 3;
const ROLE_REGISTRAR = 4;

function getAdminRoleIds(): array
{
    return [ROLE_SUPER_ADMIN, ROLE_REGISTRAR];
}

function getRoleLabel(int $roleId): string
{
    switch ($roleId) {
        case ROLE_SUPER_ADMIN:
            return 'Super Admin';
        case ROLE_REGISTRAR:
            return 'Registrar';
        case ROLE_TEACHER:
            return 'Teacher';
        case ROLE_STUDENT:
            return 'Student';
        default:
            return 'User';
    }
}

function getDashboardPathForRole(int $roleId): string
{
    switch ($roleId) {
        case ROLE_STUDENT:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Student_Files/home.php';
        case ROLE_SUPER_ADMIN:
        case ROLE_REGISTRAR:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Admin_Files/home.php';
        case ROLE_TEACHER:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Teacher_Files/home.php';
        default:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/login.php';
    }
}

function getSettingsPathForRole(int $roleId): string
{
    switch ($roleId) {
        case ROLE_STUDENT:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Student_Files/settings.php';
        case ROLE_SUPER_ADMIN:
        case ROLE_REGISTRAR:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Admin_Files/settings.php';
        case ROLE_TEACHER:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/Teacher_Files/settings.php';
        default:
            return '/Enrollment_System_CDONHS-SHS/Website_Files/login.php';
    }
}

function canManageTeacherAdvisory(int $roleId): bool
{
    return $roleId === ROLE_SUPER_ADMIN;
}

function canCreateRegistrarAccounts(int $roleId): bool
{
    return $roleId === ROLE_SUPER_ADMIN;
}

function getAdminNavigationLinks(int $roleId): array
{
    $links = [
        ['href' => 'home.php', 'label' => 'Home'],
        ['href' => 'admin_student_application_list.php', 'label' => 'Application List'],
        ['href' => 'sensitive_information.php', 'label' => 'Sensitive Information'],
        ['href' => 'activation_page.php', 'label' => 'Activation Page'],
        ['href' => 'enlistment_validation_page.php', 'label' => 'Enlistment Validation'],
        ['href' => 'document_compliance_page.php', 'label' => 'Document Compliance'],
        ['href' => 'reports_dashboard_page.php', 'label' => 'Reports Dashboard'],
        ['href' => 'document_correction_page.php', 'label' => 'Document Correction'],
        ['href' => 'audit_trail_page.php', 'label' => 'Audit Trail'],
    ];

    if (canManageTeacherAdvisory($roleId)) {
        array_splice($links, 5, 0, [[
            'href' => 'teacher_advisory_page.php',
            'label' => 'Teacher Advisory',
        ]]);
    }

    if (canCreateRegistrarAccounts($roleId)) {
        $links[] = ['href' => 'admin_creation.php', 'label' => 'Account Management'];
    }

    $links[] = ['href' => 'settings.php', 'label' => 'Settings'];

    $links[] = [
        'href' => '../../Back_End_Files/PHP_Files/logout.php',
        'label' => 'Logout',
        'class' => 'menu-link-danger',
    ];

    return $links;
}

function renderAdminHeaderCenter(string $roleLabel, string $pageTitle): string
{
    $safeRoleLabel = htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8');
    $safePageTitle = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<div class="center admin-role-banner">
    <span class="admin-role-banner__eyebrow">Admin Portal</span>
    <span class="admin-role-banner__role">{$safeRoleLabel}</span>
    <span class="admin-role-banner__page">{$safePageTitle}</span>
</div>
HTML;
}

function ensureCoreRolesExist(mysqli $connection): void
{
    $connection->query("
        INSERT INTO roles (role_id, role_name) VALUES
            (1, 'Student'),
            (2, 'Super Admin'),
            (3, 'Teacher'),
            (4, 'Registrar')
        ON DUPLICATE KEY UPDATE role_name = VALUES(role_name)
    ");
}

function redirectToPath(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function requireAuthenticatedSession(string $redirectPath): int
{
    if (!isset($_SESSION['user_id'])) {
        redirectToPath($redirectPath);
    }

    return (int) $_SESSION['user_id'];
}

function requirePortalAccess(mysqli $connection, array $allowedRoleIds, string $redirectPath): array
{
    $userId = requireAuthenticatedSession($redirectPath);
    ensureCoreRolesExist($connection);

    $allowedRoleIds = array_values(array_unique(array_map('intval', $allowedRoleIds)));
    if (empty($allowedRoleIds)) {
        redirectToPath($redirectPath);
    }

    $placeholders = implode(',', array_fill(0, count($allowedRoleIds), '?'));
    $sql = "
        SELECT u.*, r.role_name
        FROM users u
        LEFT JOIN roles r ON r.role_id = u.role_id
        WHERE u.user_id = ?
          AND u.role_id IN ($placeholders)
        LIMIT 1
    ";

    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        redirectToPath($redirectPath);
    }

    $params = array_merge([$userId], $allowedRoleIds);
    $types = 'i' . str_repeat('i', count($allowedRoleIds));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    if (!$user) {
        session_destroy();
        redirectToPath($redirectPath);
    }

    $_SESSION['role_id'] = (int) $user['role_id'];
    $_SESSION['role_name'] = $user['role_name'] ?: getRoleLabel((int) $user['role_id']);
    syncSessionThemePreference($connection, (int) $user['user_id']);

    return $user;
}

function requireAdminAccess(mysqli $connection, string $redirectPath = '../../Website_Files/login.php'): array
{
    return requirePortalAccess($connection, getAdminRoleIds(), $redirectPath);
}

function requireSuperAdminAccess(mysqli $connection, string $redirectPath = '../../Website_Files/login.php'): array
{
    return requirePortalAccess($connection, [ROLE_SUPER_ADMIN], $redirectPath);
}
