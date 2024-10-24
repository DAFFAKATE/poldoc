<?php
require_once 'config.php';

if (!checkPermission('Admin')) {
    echo json_encode(array('error' => 'Permission denied'));
    exit;
}

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page-1) * $rows;

$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
$where = '';
if (!empty($search)) {
    $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR username LIKE '%$search%'";
}

$result = array();

$rs = $conn->query("SELECT COUNT(*) FROM users $where");
$row = $rs->fetch_row();
$result["total"] = $row[0];

$rs = $conn->query("SELECT id, name, email, birth_date, nidn, username, role FROM users $where ORDER BY name LIMIT $offset,$rows");

$items = array();
while($row = $rs->fetch_assoc()){
    array_push($items, $row);
}
$result["rows"] = $items;

echo json_encode($result);

$conn->close();
?>
