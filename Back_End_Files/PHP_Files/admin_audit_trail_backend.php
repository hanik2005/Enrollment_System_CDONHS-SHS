<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../../DB_Connection/Connection.php";

$userId = (int) $_SESSION['user_id'];
$stmt = $connection->prepare("
    SELECT user_id
    FROM users
    WHERE user_id = ? AND role_id = 2
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$filterAction = trim($_GET['action_type'] ?? '');
$filterEntity = trim($_GET['entity_type'] ?? '');
$filterDateFrom = trim($_GET['date_from'] ?? '');
$filterDateTo = trim($_GET['date_to'] ?? '');
$searchText = trim($_GET['search'] ?? '');

$sql = "
    SELECT
        a.audit_id,
        a.user_id,
        COALESCE(u.username, 'Unknown') AS username,
        a.action_type,
        a.entity_type,
        a.entity_id,
        a.description,
        a.metadata,
        a.ip_address,
        a.created_at
    FROM admin_audit_trail a
    LEFT JOIN users u ON u.user_id = a.user_id
    WHERE 1 = 1
";

$params = [];
$types = '';

if ($filterAction !== '') {
    $sql .= " AND a.action_type = ?";
    $types .= 's';
    $params[] = $filterAction;
}

if ($filterEntity !== '') {
    $sql .= " AND a.entity_type = ?";
    $types .= 's';
    $params[] = $filterEntity;
}

if ($filterDateFrom !== '') {
    $sql .= " AND DATE(a.created_at) >= ?";
    $types .= 's';
    $params[] = $filterDateFrom;
}

if ($filterDateTo !== '') {
    $sql .= " AND DATE(a.created_at) <= ?";
    $types .= 's';
    $params[] = $filterDateTo;
}

if ($searchText !== '') {
    $like = '%' . $searchText . '%';
    $sql .= " AND (a.description LIKE ? OR a.entity_id LIKE ? OR u.username LIKE ?)";
    $types .= 'sss';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY a.created_at DESC LIMIT 400";

$stmt = $connection->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$auditRows = [];
while ($row = $result->fetch_assoc()) {
    $auditRows[] = $row;
}
$stmt->close();

$actionTypes = [];
$actionResult = $connection->query("SELECT DISTINCT action_type FROM admin_audit_trail ORDER BY action_type ASC");
if ($actionResult) {
    while ($row = $actionResult->fetch_assoc()) {
        $actionTypes[] = $row['action_type'];
    }
}

$entityTypes = [];
$entityResult = $connection->query("SELECT DISTINCT entity_type FROM admin_audit_trail WHERE entity_type IS NOT NULL AND entity_type <> '' ORDER BY entity_type ASC");
if ($entityResult) {
    while ($row = $entityResult->fetch_assoc()) {
        $entityTypes[] = $row['entity_type'];
    }
}
?>
