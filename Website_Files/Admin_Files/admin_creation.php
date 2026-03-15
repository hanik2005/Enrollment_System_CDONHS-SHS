<?php
session_start();

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/admin_access.php";

$admin = requireSuperAdminAccess($connection, "../login.php");
$displayName = $admin['username'] ?? "Super Admin";
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);
$selectedAccountType = trim((string) ($_GET['type'] ?? 'registrar'));
$selectedAccountType = in_array($selectedAccountType, ['registrar', 'teacher'], true) ? $selectedAccountType : 'registrar';
$successType = trim((string) ($_GET['success'] ?? ''));
$errorCode = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Account Creation</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/admin/admin_account_management.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONSHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Account Management'); ?>
        <div class="right">
            <button class="home-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="admin-creation-menu">
                <span class="menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="menu-label">Menu</span>
            </button>
        </div>
    </div>

    <div id="admin-creation-menu" class="home-menu-overlay" hidden>
        <aside class="home-menu-panel" role="dialog" aria-modal="true" aria-label="Admin navigation menu">
            <div class="home-menu-top">
                <button class="home-menu-close" type="button" aria-label="Close navigation menu">Close</button>
            </div>
            <div class="home-menu-profile">
                <img src="../../Assets/admin_profile.png" alt="Admin profile">
                <div>
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

    <main class="dashboard">
        <div class="dashboard-box account-management-shell">
            <div class="account-management-intro">
                <div>
                    <span class="account-management-tag">Super Admin Tools</span>
                    <h2>Account Management</h2>
                    <p>Create registrar accounts for enrollment validation or teacher accounts with profile details for advisory assignments.</p>
                </div>
                <div class="account-management-summary">
                    <strong><?php echo htmlspecialchars($adminRoleLabel); ?></strong>
                    <span>You control staff access, role setup, and teacher identity records from this page.</span>
                </div>
            </div>

            <?php if ($successType === 'registrar'): ?>
                <div class="account-flash account-flash-success">Registrar account created successfully.</div>
            <?php elseif ($successType === 'teacher'): ?>
                <div class="account-flash account-flash-success">Teacher account and teacher profile created successfully.</div>
            <?php elseif ($errorCode === 'missing'): ?>
                <div class="account-flash account-flash-error">Username and password are required.</div>
            <?php elseif ($errorCode === 'teacher_fields'): ?>
                <div class="account-flash account-flash-error">Teacher accounts require at least the first name and last name.</div>
            <?php elseif ($errorCode === 'username_taken'): ?>
                <div class="account-flash account-flash-error">That username already exists. Use a different username.</div>
            <?php elseif ($errorCode === 'save_failed'): ?>
                <div class="account-flash account-flash-error">Unable to save the account. Please try again.</div>
            <?php endif; ?>

            <div class="account-management-layout">
                <section class="account-creation-card">
                    <div class="account-creation-head">
                        <h3>Create a Staff Account</h3>
                        <p>Every new account starts with first-login password change enabled.</p>
                    </div>

                    <form action="../../Back_End_Files/PHP_Files/admin_creation_backend.php" method="POST" class="account-creation-form" id="accountCreationForm">
                        <div class="account-type-grid">
                            <label class="account-type-option<?php echo $selectedAccountType === 'registrar' ? ' active' : ''; ?>">
                                <input type="radio" name="account_type" value="registrar" <?php echo $selectedAccountType === 'registrar' ? 'checked' : ''; ?>>
                                <span class="account-type-title">Registrar</span>
                                <span class="account-type-copy">Enrollment validation, document review, and admin portal access.</span>
                            </label>

                            <label class="account-type-option<?php echo $selectedAccountType === 'teacher' ? ' active' : ''; ?>">
                                <input type="radio" name="account_type" value="teacher" <?php echo $selectedAccountType === 'teacher' ? 'checked' : ''; ?>>
                                <span class="account-type-title">Teacher</span>
                                <span class="account-type-copy">Creates a login plus the teacher record used by advisory assignment pages.</span>
                            </label>
                        </div>

                        <div class="account-form-grid">
                            <div class="field-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" required>
                            </div>

                            <div class="field-group">
                                <label for="password">Temporary Password</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="teacher-fields<?php echo $selectedAccountType === 'teacher' ? '' : ' is-hidden'; ?>" id="teacherFields">
                            <div class="teacher-fields-head">
                                <h4>Teacher Information</h4>
                                <p>Required for the teacher advisory and class assignment workflow.</p>
                            </div>

                            <div class="account-form-grid">
                                <div class="field-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" <?php echo $selectedAccountType === 'teacher' ? 'required' : ''; ?>>
                                </div>

                                <div class="field-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" <?php echo $selectedAccountType === 'teacher' ? 'required' : ''; ?>>
                                </div>

                                <div class="field-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name">
                                </div>

                                <div class="field-group">
                                    <label for="extension_name">Extension Name</label>
                                    <input type="text" id="extension_name" name="extension_name" placeholder="Jr., Sr., III">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="account-submit-btn">Create Account</button>
                    </form>
                </section>

                <aside class="account-guide-card">
                    <h3>Role Coverage</h3>
                    <div class="account-guide-block">
                        <strong>Registrar</strong>
                        <p>Can access shared admin validation pages but cannot access teacher advisory or account management.</p>
                    </div>
                    <div class="account-guide-block">
                        <strong>Teacher</strong>
                        <p>Gets a login account plus a teacher profile record so the user can be assigned as an adviser later.</p>
                    </div>
                    <div class="account-guide-block">
                        <strong>Security Note</strong>
                        <p>Ask new staff to change their temporary password on first login.</p>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>
    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
    <script>
        const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
        const teacherFields = document.getElementById('teacherFields');
        const teacherRequiredFields = teacherFields ? teacherFields.querySelectorAll('#first_name, #last_name') : [];

        function syncAccountTypeState() {
            const selected = document.querySelector('input[name="account_type"]:checked');
            const isTeacher = selected && selected.value === 'teacher';

            document.querySelectorAll('.account-type-option').forEach((option) => {
                const input = option.querySelector('input[name="account_type"]');
                option.classList.toggle('active', Boolean(input && input.checked));
            });

            if (teacherFields) {
                teacherFields.classList.toggle('is-hidden', !isTeacher);
            }

            teacherRequiredFields.forEach((field) => {
                field.required = isTeacher;
            });
        }

        accountTypeInputs.forEach((input) => {
            input.addEventListener('change', syncAccountTypeState);
        });

        syncAccountTypeState();
    </script>
</body>
</html>
