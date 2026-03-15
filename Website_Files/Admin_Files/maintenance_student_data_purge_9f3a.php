<?php
session_start();

include "../../DB_Connection/Connection.php";
include_once "../../Back_End_Files/PHP_Files/admin_access.php";
include_once "../../Back_End_Files/PHP_Files/audit_trail_helper.php";

$admin = requireSuperAdminAccess($connection, "../login.php");
$adminRoleLabel = getRoleLabel((int) $admin['role_id']);
$adminUserId = (int) $admin['user_id'];

if (!isset($_SESSION['student_purge_csrf'])) {
    $_SESSION['student_purge_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = (string) $_SESSION['student_purge_csrf'];

function runCount(mysqli $connection, string $sql): int
{
    $result = $connection->query($sql);
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return (int) ($row['total'] ?? 0);
}

function runDelete(mysqli $connection, string $sql): int
{
    $connection->query($sql);
    if ($connection->error) {
        throw new RuntimeException($connection->error);
    }
    return (int) $connection->affected_rows;
}

$errorMessage = "";
$successMessage = "";
$deletedStats = [];

$beforeStats = [
    'student_users' => runCount($connection, "SELECT COUNT(*) AS total FROM users WHERE role_id = " . ROLE_STUDENT),
    'student_applications' => runCount($connection, "SELECT COUNT(*) AS total FROM student_applications"),
    'students' => runCount($connection, "SELECT COUNT(*) AS total FROM students"),
    'student_strand' => runCount($connection, "SELECT COUNT(*) AS total FROM student_strand"),
    'archived_student_strand' => runCount($connection, "SELECT COUNT(*) AS total FROM archived_student_strand"),
    'student_promotion_status' => runCount($connection, "SELECT COUNT(*) AS total FROM student_promotion_status"),
    'teacher_student_notes' => runCount($connection, "SELECT COUNT(*) AS total FROM teacher_student_notes"),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');
    $confirmPhrase = trim((string) ($_POST['confirm_phrase'] ?? ''));
    $confirmCheckbox = isset($_POST['confirm_checkbox']) && $_POST['confirm_checkbox'] === '1';

    if (!hash_equals($csrfToken, $postedToken)) {
        $errorMessage = "Invalid request token. Please refresh the page and try again.";
    } elseif ($confirmPhrase !== 'DELETE ALL STUDENT DATA') {
        $errorMessage = "Confirmation phrase does not match.";
    } elseif (!$confirmCheckbox) {
        $errorMessage = "Please confirm the checkbox before deleting.";
    } else {
        try {
            $connection->begin_transaction();

            $deletedStats['teacher_student_notes'] = runDelete($connection, "DELETE FROM teacher_student_notes");
            $deletedStats['student_promotion_status'] = runDelete($connection, "DELETE FROM student_promotion_status");
            $deletedStats['archived_student_strand'] = runDelete($connection, "DELETE FROM archived_student_strand");
            $deletedStats['student_strand'] = runDelete($connection, "DELETE FROM student_strand");
            $deletedStats['students'] = runDelete($connection, "DELETE FROM students");
            $deletedStats['student_applications'] = runDelete($connection, "DELETE FROM student_applications");
            $deletedStats['student_users'] = runDelete($connection, "DELETE FROM users WHERE role_id = " . ROLE_STUDENT);

            $connection->commit();

            logAdminAudit(
                $connection,
                'STUDENT_DATA_PURGED',
                'students',
                null,
                'Super Admin purged all student-related records.',
                [
                    'deleted_counts' => $deletedStats,
                    'before_counts' => $beforeStats
                ],
                $adminUserId
            );

            $successMessage = "Student data purge completed successfully.";
        } catch (Throwable $e) {
            $connection->rollback();
            $errorMessage = "Purge failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Purge</title>
    <link rel="icon" href="../../Assets/LOGO.png" type="image/jpg">
    <link rel="stylesheet" href="../../Design/main_design.css">
    <script src="../../Back_End_Files/JSCRIPT_Files/timer-logout.js"></script>
    <style>
        .purge-container {
            max-width: 880px;
            margin: 32px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }
        .danger-box {
            border: 2px solid #b91c1c;
            background: #fef2f2;
            padding: 14px;
            border-radius: 10px;
            color: #7f1d1d;
            margin-bottom: 14px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 20px;
            font-size: 14px;
        }
        .stats-table th, .stats-table td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        .stats-table th {
            background: #1e3a8a;
            color: #fff;
        }
        .input-row {
            margin: 12px 0;
        }
        .input-row input[type="text"] {
            width: 100%;
            max-width: 420px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }
        .btn-danger {
            background: #b91c1c;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-secondary {
            margin-left: 8px;
            background: #334155;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            text-decoration: none;
            display: inline-block;
        }
        .msg-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .msg-success {
            background: #dcfce7;
            color: #166534;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        code {
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 5px;
        }
    </style>
</head>
<body <?php echo renderThemeBodyAttributes(); ?>>
    <div class="purge-container">
        <h1>Student Data Purge (Super Admin)</h1>
        <p>Logged in as: <strong><?php echo htmlspecialchars($adminRoleLabel); ?></strong></p>

        <div class="danger-box">
            This action permanently deletes all student-related data. It cannot be undone from the UI.
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="msg-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="msg-success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <h3>Current Counts</h3>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Table/Scope</th>
                    <th>Rows</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>users (role: Student)</td><td><?php echo (int) $beforeStats['student_users']; ?></td></tr>
                <tr><td>student_applications</td><td><?php echo (int) $beforeStats['student_applications']; ?></td></tr>
                <tr><td>students</td><td><?php echo (int) $beforeStats['students']; ?></td></tr>
                <tr><td>student_strand</td><td><?php echo (int) $beforeStats['student_strand']; ?></td></tr>
                <tr><td>archived_student_strand</td><td><?php echo (int) $beforeStats['archived_student_strand']; ?></td></tr>
                <tr><td>student_promotion_status</td><td><?php echo (int) $beforeStats['student_promotion_status']; ?></td></tr>
                <tr><td>teacher_student_notes</td><td><?php echo (int) $beforeStats['teacher_student_notes']; ?></td></tr>
            </tbody>
        </table>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <div class="input-row">
                <label for="confirm_phrase">Type <code>DELETE ALL STUDENT DATA</code> to confirm:</label><br>
                <input type="text" id="confirm_phrase" name="confirm_phrase" autocomplete="off" required>
            </div>
            <div class="input-row">
                <label>
                    <input type="checkbox" name="confirm_checkbox" value="1">
                    I understand this permanently deletes all student data and student accounts.
                </label>
            </div>
            <button type="submit" class="btn-danger">Delete All Student Data</button>
            <a class="btn-secondary" href="home.php">Cancel</a>
        </form>
    </div>
</body>
</html>
