<?php
require_once 'config.php';

if (!checkPermission('Admin')) {
    echo json_encode(array('error' => 'Permission denied'));
    exit;
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['errorMsg'] = 'Database error: ' . $conn->error;
    }
    
    $stmt->close();
} else {
    $response['errorMsg'] = 'Invalid request';
}

echo json_encode($response);
$conn->close();
?>
