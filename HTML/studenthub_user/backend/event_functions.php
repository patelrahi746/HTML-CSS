<?php
/**
 * Event helper functions using PDO.
 * Requires `backend/db.php` which returns $pdo instance.
 */
require_once __DIR__ . '/db.php';

/** Get all events */
function getAllEvents(): array {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/** Get a single event by ID */
function getEventById(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    return $event ?: null;
}

/** Add a new event. $posterPath can be null. */
function addEvent(array $data, ?string $posterPath = null): bool {
    global $pdo;
    $sql = 'INSERT INTO events (title, event_date, description, poster_path) VALUES (:title, :event_date, :description, :poster_path)';
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':title' => $data['title'] ?? '',
        ':event_date' => $data['event_date'] ?? null,
        ':description' => $data['description'] ?? null,
        ':poster_path' => $posterPath,
    ]);
}

/** Update an existing event. */
function updateEvent(int $id, array $data, ?string $posterPath = null): bool {
    global $pdo;
    $fields = [];
    $params = [':id' => $id];
    if (isset($data['title'])) { $fields[] = 'title = :title'; $params[':title'] = $data['title']; }
    if (isset($data['event_date'])) { $fields[] = 'event_date = :event_date'; $params[':event_date'] = $data['event_date']; }
    if (isset($data['description'])) { $fields[] = 'description = :description'; $params[':description'] = $data['description']; }
    if ($posterPath !== null) { $fields[] = 'poster_path = :poster_path'; $params[':poster_path'] = $posterPath; }
    if (empty($fields)) return false;
    $sql = 'UPDATE events SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/** Delete an event and optionally its poster file. */
function deleteEvent(int $id): bool {
    global $pdo;
    // Retrieve poster path before deletion
    $event = getEventById($id);
    if ($event && $event['poster_path']) {
        $posterFull = __DIR__ . '/../' . $event['poster_path'];
        if (file_exists($posterFull)) {
            @unlink($posterFull);
        }
    }
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
?>
