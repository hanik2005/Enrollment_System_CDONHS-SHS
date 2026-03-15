<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/DB_Connection/Connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/admin_access.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CAPTCHA check
    if (
        !isset($_POST['captcha'], $_POST['correct_sum']) ||
        $_POST['captcha'] != $_POST['correct_sum']
    ) {
        $_SESSION['login_data'] = ['username' => $_POST['username'] ?? ''];
        header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/login.php?error=captcha");
        exit();
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Get user by username ONLY
    $stmt = $connection->prepare("SELECT user_id, username, password, role_id, status, first_login FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {

            // Login success - store all needed info in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['status'] = $user['status'];
            $_SESSION['first_login'] = $user['first_login'];
            syncSessionThemePreference($connection, (int) $user['user_id']);

            if ($user['first_login'] == 1) {
                header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/change_password.php");
                exit();
            }

            switch ($user['role_id']) {
                case 1: // Student
                    header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/Student_Files/home.php");
                    break;

                case ROLE_SUPER_ADMIN:
                case ROLE_REGISTRAR:
                    header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/Admin_Files/home.php");
                    break;

                case ROLE_TEACHER:
                    header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/Teacher_Files/home.php");
                    break;

                default:
                    header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/login.php?error=role");
                    break;
    }

        
            exit();

        } else {
            // Wrong password
            header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/login.php?error=invalid");
            exit();
        }

    } else {
        // Username not found
        header("Location: /Enrollment_System_CDONHS-SHS/Website_Files/login.php?error=invalid");
        exit();
    }

    $stmt->close();
}

$connection->close();
?>
