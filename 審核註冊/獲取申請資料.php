<?php
include 'db_connection.php'; // 連接資料庫

$sql = "SELECT * FROM `帳戶註冊`";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$conn->close();
?>
