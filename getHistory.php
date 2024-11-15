<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 查詢歷史紀錄
$sql = "SELECT 使用者ID, 名字, 身份, 聯絡資訊, 帳號, 車牌號碼, 車輛類型 FROM 使用者";
$result = $conn->query($sql);

$history = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
} else {
    echo "無歷史記錄";
}

$conn->close();

// 輸出歷史紀錄為JSON格式
echo json_encode($history);
?>
