<?php
// backend/student_crud.php
// Handles AJAX CRUD operations for student records.
// Expected GET param "action" (list, add, get, update, delete).
// Responses are JSON {success:true/false, data:..., message:...}
require_once __DIR__ . '/student_functions.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $search = $_GET['search'] ?? '';
        $students = getAllStudents($search);
        echo json_encode(['success'=>true,'data'=>$students]);
        break;
    case 'add':
        // Expect POST fields: fullName, email, department
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        if ($fullName && $email && $department) {
            $ok = addStudent($fullName,$email,$department);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Student added':'Failed to add']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Missing fields']);
        }
        break;
    case 'get':
        $id = intval($_GET['id'] ?? 0);
        $student = getStudentById($id);
        if ($student) {
            echo json_encode(['success'=>true,'data'=>$student]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Student not found']);
        }
        break;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $department = trim($_POST['department'] ?? '');
        if ($id && $fullName && $email && $department) {
            $ok = updateStudent($id,$fullName,$email,$department);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Student updated':'Update failed']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Missing fields']);
        }
        break;
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $ok = deleteStudent($id);
            echo json_encode(['success'=>$ok,'message'=>$ok?'Student deleted':'Delete failed']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Missing ID']);
        }
        break;
    default:
        echo json_encode(['success'=>false,'message'=>'Invalid action']);
}
?>