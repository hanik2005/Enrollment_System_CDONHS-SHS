<?php
function renderPortalHeaderBanner(string $portalLabel, string $roleLabel, string $detailText): string
{
    $safePortalLabel = htmlspecialchars($portalLabel, ENT_QUOTES, 'UTF-8');
    $safeRoleLabel = htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8');
    $safeDetailText = htmlspecialchars($detailText, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<div class="center portal-role-banner">
    <span class="portal-role-banner__eyebrow">{$safePortalLabel}</span>
    <span class="portal-role-banner__role">{$safeRoleLabel}</span>
    <span class="portal-role-banner__page">{$safeDetailText}</span>
</div>
HTML;
}

function formatPortalPersonName(
    ?string $firstName,
    ?string $middleName,
    ?string $lastName,
    ?string $extensionName = null,
    string $fallback = 'User'
): string {
    $parts = [];

    if (!empty($firstName)) {
        $parts[] = trim($firstName);
    }

    if (!empty($middleName)) {
        $parts[] = strtoupper(substr(trim($middleName), 0, 1)) . '.';
    }

    if (!empty($lastName)) {
        $parts[] = trim($lastName);
    }

    if (!empty($extensionName)) {
        $parts[] = trim($extensionName);
    }

    $fullName = trim(implode(' ', $parts));

    return $fullName !== '' ? $fullName : $fallback;
}

function formatStudentClassSummary(
    ?string $gradeLevel,
    ?string $strandName,
    ?string $sectionName,
    bool $isGraduated = false,
    bool $isPending = false,
    bool $isRejected = false,
    bool $isPromoted = false
): string {
    if (!empty($gradeLevel) && !empty($strandName) && !empty($sectionName)) {
        return 'Grade ' . $gradeLevel . ' | ' . $strandName . ' | ' . $sectionName;
    }

    if ($isGraduated) {
        return 'Already graduated';
    }

    if ($isPending) {
        return 'Pending enlistment';
    }

    if ($isRejected) {
        return 'Rejected enlistment';
    }

    if ($isPromoted) {
        return 'Promoted';
    }

    return 'Not assigned yet';
}

function renderStudentMenuOverlay(
    string $menuId,
    string $profileImagePath,
    string $studentName,
    string $lrn,
    ?string $gradeLevel,
    ?string $strandName,
    ?string $sectionName,
    string $navContent
): string {
    $safeMenuId = htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8');
    $safeProfileImagePath = htmlspecialchars($profileImagePath, ENT_QUOTES, 'UTF-8');
    $safeStudentName = htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8');
    $safeLrn = htmlspecialchars($lrn !== '' ? $lrn : 'Not available', ENT_QUOTES, 'UTF-8');
    $safeGradeLevel = htmlspecialchars($gradeLevel ?? 'Not assigned', ENT_QUOTES, 'UTF-8');
    $safeStrandName = htmlspecialchars($strandName ?? 'Not assigned', ENT_QUOTES, 'UTF-8');
    $safeSectionName = htmlspecialchars($sectionName ?? 'Not assigned', ENT_QUOTES, 'UTF-8');

    return <<<HTML
<div id="{$safeMenuId}" class="home-menu-overlay" hidden>
    <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Student navigation menu">
        <div class="home-menu-top">
            <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
        </div>
        <div class="home-menu-profile">
            <img src="{$safeProfileImagePath}" alt="Student profile">
            <div class="home-menu-profile-copy">
                <h3>{$safeStudentName}</h3>
                <p>Student</p>
                <div class="home-menu-student-meta">
                    <span><strong>LRN:</strong> {$safeLrn}</span>
                    <span><strong>Grade Level:</strong> {$safeGradeLevel}</span>
                    <span><strong>Strand:</strong> {$safeStrandName}</span>
                    <span><strong>Section:</strong> {$safeSectionName}</span>
                </div>
            </div>
        </div>
        <nav class="home-menu-links" aria-label="Student page links">
            {$navContent}
        </nav>
    </aside>
</div>
HTML;
}
