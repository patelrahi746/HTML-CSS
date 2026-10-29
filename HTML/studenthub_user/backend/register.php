<?php
// backend/register.php
// Secure user registration using PDO, duplicate email check, and password hashing.

require_once __DIR__ . '/db.php'; // PDO connection helper

function renderResponse(string $message, string $type = 'success') {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>
<title>Registration $type</title>
<link rel='stylesheet' href='../CSS/style.css'>
</head><body>
<a href='../index.html' class='skip-link'>Back to Home</a>
<section class='register'>
<h2>Registration $type</h2>
<p style='color:" . ($type === 'success' ? 'green' : 'red') . "'>$message</p>
<a href='javascript:history.back()'>Go Back</a>
</section></body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderResponse('Invalid request method.', 'error');
}

// Retrieve and trim inputs
$fullName   = trim($_POST['fullName'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirmPassword'] ?? '';
$department = trim($_POST['department'] ?? '');

$errors = [];

// Basic validation
if (empty($fullName) || !preg_match('/^[A-Za-z]+\s+[A-Za-z]+(\s+[A-Za-z]+)*$/', $fullName)) {
    $errors[] = 'Please enter a valid full name (first and last).';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if (empty($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/', $password)) {
    $errors[] = 'Password must be at least 8 characters, include uppercase, number, and special character.';
}
if ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}
if (empty($department)) {
    $errors[] = 'Please select a department.';
}

if (!empty($errors)) {
    renderResponse(implode('<br>', $errors), 'error');
}

try {
    $pdo = getPDO();
    // Duplicate email check
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        renderResponse('An account with this email already exists.', 'error');
    }
    // Insert new user (role defaults to "student")
    $pwdHash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, department, role) VALUES (?, ?, ?, ?, ?)');
    $insert->execute([
        $fullName,
        $email,
        $pwdHash,
        $department,
        'student'
    ]);
    renderResponse('Registration successful!');
} catch (PDOException $e) {
    renderResponse('Database error: ' . $e->getMessage(), 'error');
}
?>