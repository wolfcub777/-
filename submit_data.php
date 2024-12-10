<?php
// 連接資料庫
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "資料庫連接失敗"]));
}

// 獲取前端數據
$zoneColor = isset($_POST['區域顏色']) ? trim($_POST['區域顏色']) : '';
$quantity = isset($_POST['數量']) ? intval($_POST['數量']) : null;
$remark = isset($_POST['備註']) ? trim($_POST['備註']) : null; // 新增備註欄位

// 驗證數據
if (empty($zoneColor) || $quantity === null || $quantity < 0) {
    echo json_encode(["success" => false, "error" => "無效的輸入數據"]);
    exit;
}

// 插入新的數據，新增備註欄位
$sql = "INSERT INTO 停車區域 (區域顏色, 數量, 備註, 送出時間) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $zoneColor, $quantity, $remark);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "數據已新增"]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
