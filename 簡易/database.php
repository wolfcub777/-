<?php
$servername = "127.0.0.1"; // 或 localhost
$username = "root"; // 你的 MySQL 使用者名稱
$password = ""; // 你的 MySQL 密碼，若無則留空
$database = "停車場巡查使用系統"; // 資料庫名稱，請確認與 phpMyAdmin 相同

// 建立連線
$conn = new mysqli($servername, $username, $password, $database);

// 檢查連線
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
?>
