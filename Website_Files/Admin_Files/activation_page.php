<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

/* ========================= */
/* VERIFY ADMIN SESSION      */
/* ========================= */
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($connection, "
    SELECT * FROM users 
    WHERE user_id = ?
    AND role_id = 2
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

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
$activation_query = "SELECT * FROM activation_settings ORDER BY id ASC";
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
    <link rel="stylesheet" href="../../Design/profile_dropdown.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/admin/activation_page.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONHS-SHS</span>
        </div>
        <div class="center">
            Admin
        </div>
        <div class="right">
            <button class="profile-btn" type="button">
                <img src="../../Assets/admin_profile.png">
            </button>
            <div class="profile-dropdown">
                <a href="home.php">Home</a>
                <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
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
                                        case 'Student Progress Page':
                                            echo 'Enables teachers to view and track student progress';
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
        © 2026 Cagayan De Oro National High School - Senior High School  
        <br>
        School Management System
    </div>
    
    <script src="../../Back_End_Files/JSCRIPT_Files/profile_dropdown_function.js"></script>
</body>
</html>
