<?php
require_once 'config.php';

if (!checkPermission('Admin')) {
    echo json_encode(array('error' => 'Permission denied'));
    exit;
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $birth_date = $conn->real_escape_string($_POST['birth_date']);
    $nidn = $conn->real_escape_string($_POST['nidn']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    
    if ($id == 0) {  // New user
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, birth_date, nidn, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $birth_date, $nidn, $username, $password, $role);
    } else {  // Update existing user
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, birth_date=?, nidn=?, username=?, password=?, role=? WHERE id=?");
            $stmt->bind_param("sssssssi", $name, $email, $birth_date, $nidn, $username, $password, $role, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, birth_date=?, nidn=?, username=?, role=? WHERE id=?");
            $stmt->bind_param("ssssssi", $name, $email, $birth_date, $nidn, $username, $role, $id);
        }
    }
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['errorMsg'] = 'Database error: ' . $conn->error;
    }
    
    $stmt->close();
} else {
    $response['errorMsg'] = 'Invalid request method';
}

echo json_encode($response);
$conn->close();
?>
