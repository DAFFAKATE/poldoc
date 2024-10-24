<?php
require_once 'config.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : 'Draft';
    
    if ($id == 0) {  // New document
        if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
            $response['errorMsg'] = 'File upload failed';
            echo json_encode($response);
            exit;
        }
        
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '_' . $_FILES['fileUpload']['name'];
        $filePath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($_FILES['fileUpload']['tmp_name'], $filePath)) {
            $response['errorMsg'] = 'Failed to move uploaded file';
            echo json_encode($response);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO documents (year, title, creator_id, description, upload_date, last_modified, category, status, file_path) VALUES (YEAR(CURDATE()), ?, ?, ?, NOW(), NOW(), ?, ?, ?)");
        $stmt->bind_param("sisss", $title, $_SESSION['user_id'], $description, $category, $status, $filePath);
    } else {  // Update existing document
        if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $fileName = uniqid() . '_' . $_FILES['fileUpload']['name'];
            $filePath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['fileUpload']['tmp_name'], $filePath)) {
                $response['errorMsg'] = 'Failed to move uploaded file';
                echo json_encode($response);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE documents SET title=?, description=?, last_modified=NOW(), category=?, status=?, file_path=? WHERE id=?");
            $stmt->bind_param("sssssi", $title, $description, $category, $status, $filePath, $id);
        } else {
            $stmt = $conn->prepare("UPDATE documents SET title=?, description=?, last_modified=NOW(), category=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $description, $category, $status, $id);
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