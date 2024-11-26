<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die(json_encode(["error" => "連接失敗: " . $conn->connect_error]));
}

// 查詢最新公告
$sqlLatest = "SELECT * FROM 公告 WHERE 是否最新 = 1 LIMIT 1";
$resultLatest = $conn->query($sqlLatest);

// 取得最新公告的內容
if ($resultLatest->num_rows > 0) {
    $row = $resultLatest->fetch_assoc();
    echo json_encode(["content" => $row['內容']]);
} else {
    echo json_encode(["content" => "目前沒有最新公告。"]);
}

$conn->close();
?>
