<?php
require_once 'config.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // First, get the file path
    $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $filePath = $row['file_path'];
        
        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // If database deletion was successful, delete the file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $response['success'] = true;
        } else {
            $response['errorMsg'] = 'Database error: ' . $conn->error;
        }
    } else {
        $response['errorMsg'] = 'Document not found';
    }
    
    $stmt->close();
} else {
    $response['errorMsg'] = 'Invalid request';
}

echo json_encode($response);
$conn->close();
?>
