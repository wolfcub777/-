<?php
$servername = "localhost";  // 資料庫伺服器
$username = "root";         // 資料庫使用者名稱
$password = "";             // 資料庫密碼，通常是空白
$dbname = "停車場巡查使用系統";  // 你的資料庫名稱

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}
?>
