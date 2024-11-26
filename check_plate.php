<?php
// 設定資料庫連接
$servername = "localhost";
$username = "root"; // 修改為你的資料庫用戶名
$password = ""; // 修改為你的資料庫密碼
$dbname = "停車場巡查使用系統";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die(json_encode(['error' => '連接失敗: ' . $conn->connect_error]));
}

// 取得車牌號碼
$plate = isset($_GET['plate']) ? $conn->real_escape_string($_GET['plate']) : '';

$sql = "SELECT COUNT(*) AS count FROM 使用者 WHERE 車牌號碼 = '$plate'";
$result = $conn->query($sql);

$response = ['found' => false];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $response['found'] = true;
    }
}

$conn->close();
echo json_encode($response);
?>
