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

// 查詢所有備註記錄
$sql = "SELECT 區域顏色, 備註, 送出時間 FROM 停車區域 ORDER BY 送出時間 ASC";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "無法找到備註數據"]);
    exit;
}

// 構建數據陣列
$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = [
        "區域顏色" => $row["區域顏色"],
        "備註" => $row["備註"] ?? "無備註", // 預設為「無備註」
        "送出時間" => $row["送出時間"]
    ];
}

$conn->close();

// 回傳數據
echo json_encode(["success" => true, "data" => $notes]);
?>
