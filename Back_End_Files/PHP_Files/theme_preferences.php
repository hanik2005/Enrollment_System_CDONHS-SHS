<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const THEME_LIGHT = 'light';
const THEME_DARK = 'dark';

function normalizeThemePreference(?string $theme): string
{
    return $theme === THEME_DARK ? THEME_DARK : THEME_LIGHT;
}

function ensureUserThemePreferencesTable(mysqli $connection): void
{
    static $tableChecked = false;

    if ($tableChecked) {
        return;
    }

    $connection->query("
        CREATE TABLE IF NOT EXISTS user_theme_preferences (
            user_id INT(11) NOT NULL,
            theme_preference VARCHAR(10) NOT NULL DEFAULT 'light',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $tableChecked = true;
}

function loadThemePreference(mysqli $connection, int $userId): string
{
    if ($userId <= 0) {
        return THEME_LIGHT;
    }

    ensureUserThemePreferencesTable($connection);

    $stmt = $connection->prepare("
        SELECT theme_preference
        FROM user_theme_preferences
        WHERE user_id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return THEME_LIGHT;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    return normalizeThemePreference($row['theme_preference'] ?? null);
}

function syncSessionThemePreference(mysqli $connection, ?int $userId = null): string
{
    $sessionTheme = $_SESSION['theme_preference'] ?? null;
    $normalizedSessionTheme = normalizeThemePreference($sessionTheme);

    if (is_string($sessionTheme) && $sessionTheme === $normalizedSessionTheme) {
        return $normalizedSessionTheme;
    }

    $resolvedUserId = $userId ?? (isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0);
    $theme = $resolvedUserId > 0
        ? loadThemePreference($connection, $resolvedUserId)
        : THEME_LIGHT;

    $_SESSION['theme_preference'] = $theme;

    return $theme;
}

function saveThemePreference(mysqli $connection, int $userId, ?string $theme): string
{
    $normalizedTheme = normalizeThemePreference($theme);

    if ($userId <= 0) {
        $_SESSION['theme_preference'] = $normalizedTheme;
        return $normalizedTheme;
    }

    ensureUserThemePreferencesTable($connection);

    $stmt = $connection->prepare("
        INSERT INTO user_theme_preferences (user_id, theme_preference)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE
            theme_preference = VALUES(theme_preference),
            updated_at = CURRENT_TIMESTAMP
    ");

    if ($stmt) {
        $stmt->bind_param('is', $userId, $normalizedTheme);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['theme_preference'] = $normalizedTheme;

    return $normalizedTheme;
}

function getCurrentThemePreference(): string
{
    return normalizeThemePreference($_SESSION['theme_preference'] ?? THEME_LIGHT);
}

function getThemeLabel(?string $theme = null): string
{
    return normalizeThemePreference($theme ?? getCurrentThemePreference()) === THEME_DARK
        ? 'Dark Mode'
        : 'Light Mode';
}

function renderThemeBodyAttributes(string $bodyClasses = ''): string
{
    $theme = getCurrentThemePreference();
    $classes = trim($bodyClasses);
    $classes = trim($classes . ' theme-enabled theme-' . $theme);

    return 'class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '" data-theme="' . htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') . '"';
}
