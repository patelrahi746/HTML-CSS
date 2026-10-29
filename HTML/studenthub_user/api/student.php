<?php
/**
 * API endpoint for Student CRUD operations (admin UI).
 * Supports both GET (read) and POST (write) actions.
 * Returns JSON with appropriate HTTP status codes.
 */
require_once __DIR__ . '/../backend/student_functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid request'];

switch (strtolower($method)) {
    case 'get':
        // Read operations via query string
        if ($action === 'list') {
            $students = getAllStudents();
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $students]);
            exit;
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $student = getStudentById((int)$_GET['id']);
            if ($student) {
                http_response_code(200);
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Student not found']);
            }
            exit;
        }
        break;
    case 'post':
        // Write operations via POST body
        if ($action === 'add') {
            $added = addStudent([
                'full_name'   => $_POST['full_name'] ?? '',
                'email'       => $_POST['email'] ?? '',
                'password'    => $_POST['password'] ?? '',
                'department'  => $_POST['department'] ?? '',
                'role'        => $_POST['role'] ?? 'student',
            ]);
            if ($added) {
                http_response_code(201);
                echo json_encode(['success' => true, 'message' => 'Student added']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to add student']);
            }
            exit;
        } elseif ($action === 'update' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $data = [];
            if (isset($_POST['full_name'])) $data['full_name'] = $_POST['full_name'];
            if (isset($_POST['email'])) $data['email'] = $_POST['email'];
            if (isset($_POST['password'])) $data['password'] = $_POST['password'];
            if (isset($_POST['department'])) $data['department'] = $_POST['department'];
            if (isset($_POST['role'])) $data['role'] = $_POST['role'];
            $updated = updateStudent($id, $data);
            if ($updated) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Student updated']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Update failed']);
            }
            exit;
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            $deleted = deleteStudent((int)$_POST['id']);
            if ($deleted) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Student deleted']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Delete failed']);
            }
            exit;
        }
        break;
}
// Fallback for unsupported methods / actions
http_response_code(400);
echo json_encode($response);
?>
