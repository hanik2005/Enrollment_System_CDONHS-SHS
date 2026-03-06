<?php
session_start();

$feature_name = isset($_GET['feature']) ? htmlspecialchars($_GET['feature']) : 'this feature';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - CDONHS-SHS</title>
    <link rel="icon" href="Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="Design/main_design.css">
    <link rel="stylesheet" href="Design/access_denied_design.css">
    <style>
      
    </style>
</head>
<body>
    <div class="access-denied-container">
        <div class="warning-icon">🚫</div>
        <h1>Access Denied</h1>
        <p>
            Sorry, <strong><?php echo $feature_name; ?></strong> is currently disabled.<br>
            Please contact the administrator for more information.
        </p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
            $role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
            $home_link = '';
            if ($role_id == 2) {
                $home_link = '/Enrollment_System_CDONHS-SHS/Website_Files/Admin_Files/home.php';
            } elseif ($role_id == 3) {
                $home_link = '/Enrollment_System_CDONHS-SHS/Website_Files/Teacher_Files/home.php';
            } elseif ($role_id == 1) {
                $home_link = '/Enrollment_System_CDONHS-SHS/Website_Files/Student_Files/home.php';
            }else{
                $home_link = '/Enrollment_System_CDONHS-SHS/Website_Files/guest_page.php';
            }
            ?>
            <a href="<?php echo $home_link; ?>" class="back-btn">Go to Home</a>
        <?php else: ?>
            <a href="/Enrollment_System_CDONHS-SHS/Website_Files/guest_page.php" class="back-btn">Go to Guest Page</a>
        <?php endif; ?>
    </div>
</body>
</html>
