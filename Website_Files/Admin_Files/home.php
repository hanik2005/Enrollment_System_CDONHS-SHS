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

// Prepare statement
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/profile_dropdown.css">
     <link rel="stylesheet" href="../../Design/dashboard_design.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <!-- header -->
    <div class="header">
    <div class="left">
        <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
        <span>CDONSHS-SHS</span>
    </div>
    <div class="center">
        Admin
    </div>
    <div class="right">
         <button class="profile-btn" type="button" aria-label="Open profile menu">
        <img src="../../Assets/admin_profile.png" alt="Admin profile">
    </button>

    <div class="profile-dropdown">
        <a href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>

    </div>
    </div>
    </div>

<main id="main-content">
    <div class="dashboard">

  <div class="dashboard-box">

    <div class="dashboard-wrapper">

    <div class="dashboard-container">
        <a href="admin_student_application_list.php" class="dashboard-card">
            <img src="../../Assets/application_button.jpg" alt="Application List icon">
            <h3>Application List</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="sensitive_information.php" class="dashboard-card">
            <img src="../../Assets/Visible.png" alt="Sensitive Information icon">
            <h3>Sensitive Information</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="activation_page.php" class="dashboard-card">
            <img src="../../Assets/activation_button.png" alt="Activation Page icon">
            <h3>Activation Page</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="enlistment_validation_page.php" class="dashboard-card">
            <img src="../../Assets/enlistment_validation.png" alt="Enlistment Validation icon">
            <h3>Enlistment Validation</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="teacher_advisory_page.php" class="dashboard-card">
            <img src="../../Assets/teacher_application_image.png" alt="Teacher Advisory icon">
            <h3>Teacher Advisory</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="document_compliance_page.php" class="dashboard-card">
            <img src="../../Assets/Visible.png" alt="Document Compliance icon">
            <h3>Document Compliance</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="reports_dashboard_page.php" class="dashboard-card">
            <img src="../../Assets/enlistment_validation.png" alt="Reports Dashboard icon">
            <h3>Reports Dashboard</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="document_correction_page.php" class="dashboard-card">
            <img src="../../Assets/application_button.jpg" alt="Document Correction icon">
            <h3>Document Correction</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="audit_trail_page.php" class="dashboard-card">
            <img src="../../Assets/activation_button.png" alt="Audit Trail icon">
            <h3>Audit Trail</h3>
        </a>
    </div>

    <div class="dashboard-container">
        <a href="student_progress_validation_page.php" class="dashboard-card">
            <img src="../../Assets/progress_button.png" alt="Student Progress Validation icon">
            <h3>Student Progress Validation</h3>
        </a>
    </div>

    </div>
  </div>

</div>
</main>



     <!-- footer -->
    <div class="footer">
    &copy; 2026 Cagayan De Oro National High School - Senior High School  
    <br>
    School Management System
    </div>
    <script src="../../Back_End_Files/JSCRIPT_Files/profile_dropdown_function.js"></script>
</body>
</html>

