<?php
/**
 * API endpoint for Event CRUD operations (admin UI).
 * Supports GET (read) and POST (write) actions, similar to student API.
 */
require_once __DIR__ . '/../backend/event_functions.php';
require_once __DIR__ . '/../backend/audit_log.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Assume admin is authenticated; retrieve admin user ID from session if available
session_start();
$adminId = $_SESSION['user_id'] ?? null;

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

switch (strtolower($method)) {
    case 'get':
        if ($action === 'list') {
            $events = getAllEvents();
            respond(['success'=>true,'data'=>$events]);
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $event = getEventById((int)$_GET['id']);
            if ($event) {
                respond(['success'=>true,'data'=>$event]);
            } else {
                respond(['success'=>false,'message'=>'Event not found'],404);
            }
        }
        break;
    case 'post':
        if ($action === 'add') {
            // Use same upload handling as backend/event_crud.php but through API
            // For simplicity, reuse event_crud logic via include (not ideal). We'll just call addEvent with posted data.
            $posterPath = null;
            if (!empty($_FILES['poster']['name'])) {
                $allowed = ['image/jpeg','image/png','image/gif'];
                if (in_array($_FILES['poster']['type'],$allowed) && $_FILES['poster']['size'] <= 2*1024*1024) {
                    $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                    $newName = 'poster_'.time().'_'.uniqid().'.'.$ext;
                    $destDir = __DIR__ . '/../uploads/posters/';
                    if (!is_dir($destDir)) { mkdir($destDir,0755,true); }
                    $destPath = $destDir . $newName;
                    if (move_uploaded_file($_FILES['poster']['tmp_name'],$destPath)) {
                        $posterPath = 'uploads/posters/'.$newName;
                    } else {
                        respond(['success'=>false,'message'=>'Failed to move uploaded file'],500);
                    }
                } else {
                    respond(['success'=>false,'message'=>'Invalid poster file (type/size)'],400);
                }
            }
            $added = addEvent([
                'title'=>$_POST['title']??'',
                'event_date'=>$_POST['event_date']??null,
                'description'=>$_POST['description']??''
            ], $posterPath);
            if ($added) {
                if ($adminId) { logAction($adminId, 'Added event: '.$_POST['title']); }
                respond(['success'=>true,'message'=>'Event added'],201);
            } else {
                respond(['success'=>false,'message'=>'Failed to add event'],500);
            }
        } elseif ($action === 'update' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $data = [];
            if (isset($_POST['title'])) $data['title'] = $_POST['title'];
            if (isset($_POST['event_date'])) $data['event_date'] = $_POST['event_date'];
            if (isset($_POST['description'])) $data['description'] = $_POST['description'];
            $posterPath = null;
            if (!empty($_FILES['poster']['name'])) {
                $allowed = ['image/jpeg','image/png','image/gif'];
                if (in_array($_FILES['poster']['type'],$allowed) && $_FILES['poster']['size'] <= 2*1024*1024) {
                    $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                    $newName = 'poster_'.time().'_'.uniqid().'.'.$ext;
                    $destDir = __DIR__ . '/../uploads/posters/';
                    if (!is_dir($destDir)) { mkdir($destDir,0755,true); }
                    $destPath = $destDir . $newName;
                    if (move_uploaded_file($_FILES['poster']['tmp_name'],$destPath)) {
                        $posterPath = 'uploads/posters/'.$newName;
                    } else { respond(['success'=>false,'message'=>'Failed to move uploaded file'],500); }
                } else { respond(['success'=>false,'message'=>'Invalid poster file (type/size)'],400); }
            }
            $updated = updateEvent($id,$data,$posterPath);
            if ($updated) {
                if ($adminId) { logAction($adminId, 'Updated event ID '.$id); }
                respond(['success'=>true,'message'=>'Event updated']);
            } else {
                respond(['success'=>false,'message'=>'Update failed'],500);
            }
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $deleted = deleteEvent($id);
            if ($deleted) {
                if ($adminId) { logAction($adminId, 'Deleted event ID '.$id); }
                respond(['success'=>true,'message'=>'Event deleted']);
            } else {
                respond(['success'=>false,'message'=>'Delete failed'],500);
            }
        }
        break;
}
// Unsupported method/action
respond(['success'=>false,'message'=>'Invalid request'],400);
?>
