<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($connection, "
    SELECT *
    FROM users
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

$displayName = $admin['username'] ?? "Administrator";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Creation</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <link rel="stylesheet" href="../../Design/dashboard_design.css">
    <link rel="stylesheet" href="../../Design/home_pages_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
</head>
<body>
    <div class="header">
        <div class="left">
            <img src="../../Assets/LOGO.png" alt="CDONSHS Logo">
            <span>CDONSHS-SHS</span>
        </div>
        <div class="center">Admin Creation</div>
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
                    <p>Administrator</p>
                </div>
            </div>
            <nav class="home-menu-links" aria-label="Admin page links">
                <a href="home.php">Home</a>
                <a href="admin_student_application_list.php">Application List</a>
                <a class="menu-link-danger" href="../../Back_End_Files/PHP_Files/logout.php">Logout</a>
            </nav>
        </aside>
    </div>

    <main class="dashboard">
        <div class="dashboard-box">
            <h2>Create Admin Account</h2>
            <form action="../../Back_End_Files/PHP_Files/admin_creation_backend.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div style="margin-top:10px;">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" style="margin-top:14px;">Submit</button>
            </form>
        </div>
    </main>

    <div class="footer">
        &copy; 2026 Cagayan De Oro National High School - Senior High School
        <br>
        School Management System
    </div>
    <script src="../../Back_End_Files/JSCRIPT_Files/home_hamburger_menu.js"></script>
</body>
</html>
