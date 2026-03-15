<?php
session_start();

include "../../DB_Connection/Connection.php";
include "../../Back_End_Files/PHP_Files/admin_access.php";

$admin = requireAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);
$navLinks = getAdminNavigationLinks((int) $admin['role_id']);

/* ========================= */
/* HANDLE ACTIVATION TOGGLE  */
/* ========================= */
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activation_id'])) {
    $activation_id = intval($_POST['activation_id']);
    $current_status = intval($_POST['current_status']);
    $new_status = $current_status === 1 ? 0 : 1;
    
    $update_stmt = mysqli_prepare($connection, "
        UPDATE activation_settings 
        SET activation_status = ? 
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($update_stmt, "ii", $new_status, $activation_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        $message = 'Activation status updated successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error updating activation status.';
        $message_type = 'error';
    }
    mysqli_stmt_close($update_stmt);
}

/* ========================= */
/* FETCH ACTIVATION SETTINGS */
/* ========================= */
$activation_query = "SELECT * FROM activation_settings WHERE activation_name <> 'Student Progress Page' ORDER BY id ASC";
$activation_result = mysqli_query($connection, $activation_query);

$activation_settings = [];
while ($row = mysqli_fetch_assoc($activation_result)) {
    $activation_settings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation Settings - CDONHS-SHS Admin</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/admin/activation_design.css">
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <!-- Header -->
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <?php echo renderAdminHeaderCenter($adminRoleLabel, 'Activation Settings'); ?>
        <div class="right">
            <button class="legacy-menu-trigger" type="button">
                <img src="../../Assets/admin_profile.png">
            </button>
            <div class="legacy-nav-links">
                <?php foreach ($navLinks as $link): ?>
                    <a href="<?php echo htmlspecialchars($link['href']); ?>"<?php echo isset($link['class']) ? ' class="' . htmlspecialchars($link['class']) . '"' : ''; ?>>
                        <?php echo htmlspecialchars($link['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
        <div class="dashboard-box">
            <div class="content-area">
                <a href="home.php" class="back-btn">← Back to Home</a>
                
                <div class="page-title">
                    <h2>Activation Settings</h2>
                </div>
                
                <?php if ($message): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="activation-list">
                    <?php foreach ($activation_settings as $setting): ?>
                        <div class="activation-card">
                            <div class="activation-info">
                                <h3>
                                    <?php echo htmlspecialchars($setting['activation_name']); ?>
                                    <span class="status-badge <?php echo $setting['activation_status'] == 1 ? 'status-enabled' : 'status-disabled'; ?>">
                                        <?php echo $setting['activation_status'] == 1 ? 'Enabled' : 'Disabled'; ?>
                                    </span>
                                </h3>
                                <p>
                                    <?php 
                                    switch($setting['activation_name']) {
                                        case 'Student Enrollment':
                                            echo 'Controls student enrollment and online registration';
                                            break;
                                        case 'Form 137 and 138 Page':
                                            echo 'Allows students to view their Form 137 and 138 records';
                                            break;
                                        case 'Teacher Registration':
                                            echo 'Allows new teachers to register and apply';
                                            break;
                                        default:
                                            echo 'Feature activation setting';
                                    }
                                    ?>
                                </p>
                            </div>
                            
                            <form method="POST" action="" class="toggle-form">
                                <input type="hidden" name="activation_id" value="<?php echo $setting['id']; ?>">
                                <input type="hidden" name="current_status" value="<?php echo $setting['activation_status']; ?>">
                                <button type="submit" class="toggle-btn <?php echo $setting['activation_status'] == 1 ? 'disable' : 'enable'; ?>">
                                    <?php echo $setting['activation_status'] == 1 ? 'Disable' : 'Enable'; ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
    </div>
    
    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
