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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Creation</title>
</head>
<body>
    <form action="../Back_End_Files/PHP_Files/admin_creation_backend.php" 
      method="POST" enctype="multipart/form-data">
         <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

         <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>


        <button type="submit">Submit</button>


    </form>
</body>
</html>