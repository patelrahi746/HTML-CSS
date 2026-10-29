<?php
/**
 * registration_stats.php – returns counts for students, events, registrations.
 * Used by admin dashboard statistics section.
 */
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

try {
    $stats = [];
    $stats['students'] = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $stats['events'] = $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $stats['registrations'] = $pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
    echo json_encode(['success'=>true,'stats'=>$stats]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Error fetching stats']);
}
?>
