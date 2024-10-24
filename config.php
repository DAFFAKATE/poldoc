<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'poltek_jambi_docs');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function checkPermission($requiredRole) {
    $userRole = getUserRole();
    if ($userRole === 'KETUA LP3M') return true;
    if ($userRole === 'Admin' && $requiredRole !== 'KETUA LP3M') return true;
    return $userRole === $requiredRole;
}
?>
