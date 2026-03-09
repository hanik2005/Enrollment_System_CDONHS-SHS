<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/DB_Connection/Connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Enrollment_System_CDONHS-SHS/Back_End_Files/PHP_Files/admin_access.php';

$admin = requireSuperAdminAccess($connection);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../Website_Files/Admin_Files/admin_creation.php");
    exit;
}

$accountType = trim((string) ($_POST['account_type'] ?? 'registrar'));
$accountType = in_array($accountType, ['registrar', 'teacher'], true) ? $accountType : 'registrar';
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$middleName = trim((string) ($_POST['middle_name'] ?? ''));
$extensionName = trim((string) ($_POST['extension_name'] ?? ''));

$redirectBase = "../../Website_Files/Admin_Files/admin_creation.php?type=" . urlencode($accountType);

if ($username === '' || $password === '') {
    header("Location: {$redirectBase}&error=missing");
    exit;
}

if ($accountType === 'teacher' && ($firstName === '' || $lastName === '')) {
    header("Location: {$redirectBase}&error=teacher_fields");
    exit;
}

$duplicateStmt = $connection->prepare("SELECT user_id FROM users WHERE username = ? LIMIT 1");
$duplicateStmt->bind_param("s", $username);
$duplicateStmt->execute();
$duplicateUser = $duplicateStmt->get_result()->fetch_assoc();
$duplicateStmt->close();

if ($duplicateUser) {
    header("Location: {$redirectBase}&error=username_taken");
    exit;
}

$roleId = $accountType === 'teacher' ? ROLE_TEACHER : ROLE_REGISTRAR;
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$connection->begin_transaction();

try {
    $insertUser = $connection->prepare("
        INSERT INTO users (username, password, role_id)
        VALUES (?, ?, ?)
    ");
    $insertUser->bind_param("ssi", $username, $hashedPassword, $roleId);

    if (!$insertUser->execute()) {
        throw new RuntimeException('Unable to create user account.');
    }

    $createdUserId = (int) $connection->insert_id;
    $insertUser->close();

    if ($accountType === 'teacher') {
        $insertTeacher = $connection->prepare("
            INSERT INTO teachers (user_id, first_name, last_name, middle_name, extension_name)
            VALUES (?, ?, ?, ?, ?)
        ");
        $middleNameValue = $middleName !== '' ? $middleName : null;
        $extensionNameValue = $extensionName !== '' ? $extensionName : null;
        $insertTeacher->bind_param(
            "issss",
            $createdUserId,
            $firstName,
            $lastName,
            $middleNameValue,
            $extensionNameValue
        );

        if (!$insertTeacher->execute()) {
            throw new RuntimeException('Unable to save teacher information.');
        }

        $insertTeacher->close();
    }

    $connection->commit();
    header("Location: {$redirectBase}&success={$accountType}");
    exit;
} catch (Throwable $exception) {
    $connection->rollback();
    header("Location: {$redirectBase}&error=save_failed");
    exit;
}
