<?php
require_once 'config.php';

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1) * $rows;

$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
$where = '';

if (!empty($search)) {
    $where = "WHERE (d.title LIKE '%$search%' OR d.description LIKE '%$search%' OR u.name LIKE '%$search%')";
}

// Tambahkan filter untuk user dengan role "User" agar hanya melihat dokumen yang "Approve"
$userRole = getUserRole();
if ($userRole === 'User') {
    $where .= (empty($where) ? "WHERE" : " AND") . " d.status = 'Approve'";
}

$result = array();

$rs = $conn->query("SELECT COUNT(*) FROM documents d JOIN users u ON d.creator_id = u.id $where");
$row = $rs->fetch_row();
$result["total"] = $row[0];

$rs = $conn->query("SELECT d.*, u.name as creator FROM documents d JOIN users u ON d.creator_id = u.id $where ORDER BY d.upload_date DESC LIMIT $offset,$rows");

$items = array();
while($row = $rs->fetch_assoc()){
    array_push($items, $row);
}
$result["rows"] = $items;

echo json_encode($result);

$conn->close();
?>
