<?php

function getClientIpAddress(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $value = explode(',', (string) $_SERVER[$key])[0];
            return trim($value);
        }
    }
    return 'UNKNOWN';
}

function logAdminAudit(
    mysqli $connection,
    string $actionType,
    ?string $entityType,
    ?string $entityId,
    string $description,
    array $metadata = [],
    ?int $userId = null
): bool {
    if ($userId === null) {
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    $metadataJson = !empty($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null;
    $ipAddress = getClientIpAddress();

    $stmt = $connection->prepare("
        INSERT INTO admin_audit_trail
            (user_id, action_type, entity_type, entity_id, description, metadata, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("Audit prepare failed: " . $connection->error);
        return false;
    }

    $stmt->bind_param(
        "issssss",
        $userId,
        $actionType,
        $entityType,
        $entityId,
        $description,
        $metadataJson,
        $ipAddress
    );

    $ok = $stmt->execute();
    if (!$ok) {
        error_log("Audit insert failed: " . $stmt->error);
    }
    $stmt->close();

    return $ok;
}
