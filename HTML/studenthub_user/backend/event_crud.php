<?php
/**
 * Event CRUD endpoint for admin UI (AJAX).
 * Expects POST with 'action' parameter: list, get, add, update, delete.
 */
require_once __DIR__ . '/event_functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid request'];

switch ($action) {
    case 'list':
        $events = getAllEvents();
        $response = ['success' => true, 'data' => $events];
        break;
    case 'get':
        $id = intval($_POST['id'] ?? 0);
        $event = getEventById($id);
        if ($event) {
            $response = ['success' => true, 'data' => $event];
        } else {
            $response['message'] = 'Event not found';
        }
        break;
    case 'add':
        $title = $_POST['title'] ?? '';
        $event_date = $_POST['event_date'] ?? null;
        $description = $_POST['description'] ?? '';
        // Handle poster upload
        $posterPath = null;
        if (!empty($_FILES['poster']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['poster']['type'], $allowed) && $_FILES['poster']['size'] <= 2*1024*1024) {
                $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                $newName = 'poster_' . time() . '_' . uniqid() . '.' . $ext;
                $destDir = __DIR__ . '/../uploads/posters/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $destPath = $destDir . $newName;
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $destPath)) {
                    $posterPath = 'uploads/posters/' . $newName; // relative to project root
                } else {
                    $response['message'] = 'Failed to move uploaded file';
                    break;
                }
            } else {
                $response['message'] = 'Invalid poster file (type/size)';
                break;
            }
        }
        $added = addEvent(['title'=>$title,'event_date'=>$event_date,'description'=>$description], $posterPath);
        if ($added) {
            $response = ['success' => true, 'message' => 'Event added'];
        } else {
            $response['message'] = 'Failed to add event';
        }
        break;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $data = [];
        if (isset($_POST['title'])) $data['title'] = $_POST['title'];
        if (isset($_POST['event_date'])) $data['event_date'] = $_POST['event_date'];
        if (isset($_POST['description'])) $data['description'] = $_POST['description'];
        // Poster handling: optional new file
        $posterPath = null;
        if (!empty($_FILES['poster']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['poster']['type'], $allowed) && $_FILES['poster']['size'] <= 2*1024*1024) {
                $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                $newName = 'poster_' . time() . '_' . uniqid() . '.' . $ext;
                $destDir = __DIR__ . '/../uploads/posters/';
                if (!is_dir($destDir)) { mkdir($destDir, 0755, true); }
                $destPath = $destDir . $newName;
                if (move_uploaded_file($_FILES['poster']['tmp_name'], $destPath)) {
                    $posterPath = 'uploads/posters/' . $newName;
                } else { $response['message'] = 'Failed to move uploaded file'; break; }
            } else { $response['message'] = 'Invalid poster file (type/size)'; break; }
        }
        $updated = updateEvent($id, $data, $posterPath);
        $response = $updated ? ['success'=>true,'message'=>'Event updated'] : ['success'=>false,'message'=>'Update failed'];
        break;
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        $deleted = deleteEvent($id);
        $response = $deleted ? ['success'=>true,'message'=>'Event deleted'] : ['success'=>false,'message'=>'Delete failed'];
        break;
    default:
        $response['message'] = 'Unsupported action';
}

echo json_encode($response);
?>
