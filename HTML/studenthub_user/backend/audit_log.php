<?php
/**
 * Audit Log helper – records admin actions.
 * Table schema is defined in schema.sql (audit_log).
 */
require_once __DIR__ . '/db.php';

/**
 * Log an admin action.
 * @param int $userId ID of the admin performing the action.
 * @param string $action Human‑readable description of the action.
 * @return bool Success status.
 */
function logAction(int $userId, string $action): bool {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO audit_log (user_id, action) VALUES (:user_id, :action)');
    return $stmt->execute([':user_id' => $userId, ':action' => $action]);
}
?>
