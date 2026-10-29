<?php
// backend/student_functions.php
// Common CRUD helpers for managing student users (role = 'student')
require_once __DIR__ . '/db.php';

/** Retrieve PDO instance */
function getDB(): PDO { return getPDO(); }

/** Get all students optionally filtered by a search term */
function getAllStudents(string $search = ''): array {
    $pdo = getDB();
    $sql = "SELECT id, full_name, email, department, created_at FROM users WHERE role = 'student'";
    $params = [];
    if ($search !== '') {
        $sql .= " AND (full_name LIKE :s OR email LIKE :s OR department LIKE :s)";
        $params[':s'] = "%$search%";
    }
    $sql .= " ORDER BY full_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Get a single student by ID */
function getStudentById(int $id): ?array {
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT id, full_name, email, department, created_at FROM users WHERE id = ? AND role = "student"');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Add a new student (password is auto‑generated) */
function addStudent(string $fullName, string $email, string $department): bool {
    $pdo = getDB();
    // Generate a temporary password and hash it
    $tempPass = bin2hex(random_bytes(4)); // 8‑char hex string
    $hash = password_hash($tempPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, department, role) VALUES (?, ?, ?, ?, "student")');
    return $stmt->execute([$fullName, $email, $hash, $department]);
}

/** Update existing student (no password change) */
function updateStudent(int $id, string $fullName, string $email, string $department): bool {
    $pdo = getDB();
    $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, department = ? WHERE id = ? AND role = "student"');
    return $stmt->execute([$fullName, $email, $department, $id]);
}

/** Delete a student */
function deleteStudent(int $id): bool {
    $pdo = getDB();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND role = "student"');
    return $stmt->execute([$id]);
}
?>