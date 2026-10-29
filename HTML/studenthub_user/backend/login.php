<?php
// backend/login.php
// Secure login handling with password verification, session management, role‑based redirect,
// session timeout, and optional "Remember Me" cookie.

require_once __DIR__ . '/db.php'; // PDO helper

// Helper to render a simple HTML response (used for error cases)
function renderResponse(string $message, string $type = 'error') {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>
<title>Login $type</title>
<link rel='stylesheet' href='../CSS/style.css'>
</head><body>
<a href='../index.html' class='skip-link'>Back to Home</a>
<section class='login'>
<h2>Login $type</h2>
<p style='color:red;'>$message</p>
<a href='javascript:history.back()'>Go Back</a>
</section></body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderResponse('Invalid request method.');
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['rememberMe']);

if (empty($email) || empty($password)) {
    renderResponse('Email and password are required.');
}

try {
    $pdo = getPDO();
    // Fetch user by email
    $stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        renderResponse('Invalid email or password.');
    }

    // Start session & set session vars
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['last_activity'] = time(); // for timeout

    // Remember‑me handling (simple version)
    if ($remember) {
        // Generate random token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        // Store token hash in DB (create table if not exists)
        $pdo->exec("CREATE TABLE IF NOT EXISTS remember_tokens (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token_hash CHAR(64) NOT NULL, expires_at DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE);");
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $insert = $pdo->prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
        $insert->execute([$user['id'], $tokenHash, $expires]);
        // Set cookie (HttpOnly, Secure for production)
        setcookie('remember_me', $token, time() + 30*24*60*60, '/', '', false, true);
    }

    // Role‑based redirect
    if ($user['role'] === 'admin') {
        header('Location: ../admin.html'); // placeholder admin page (can be created later)
    } else {
        header('Location: ../dashboard.html');
    }
    exit;
} catch (PDOException $e) {
    renderResponse('Database error: ' . $e->getMessage());
}
?>