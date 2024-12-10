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

// 獲取 URL 參數
$zoneColor = isset($_GET['zoneColor']) ? $_GET['zoneColor'] : '';
if (empty($zoneColor)) {
    echo json_encode(["success" => false, "message" => "未指定區域顏色！"]);
    exit;
}

// 查詢該區域的所有記錄，包含備註
$sql = "SELECT 數量, 送出時間, 備註 FROM 停車區域 WHERE 區域顏色 = ? ORDER BY 送出時間 ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $zoneColor);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "找不到該區域的數據！"]);
    exit;
}

// 構建數據陣列
$data = [];
$totalSpaces = 454; // 總停車格數
$maxSpaces = 71;    // 設定該區域最大停車格數，需根據實際情況動態設置
$latestUsedSpaces = 0;

while ($row = $result->fetch_assoc()) {
    $data[] = [
        '數量' => intval($row['數量']),
        '送出時間' => $row['送出時間'],
        '備註' => $row['備註'] // 加入備註
    ];
    $latestUsedSpaces = intval($row['數量']); // 更新最新的數量
}

$stmt->close();
$conn->close();

// 計算統計數據
$zoneUsageRate = ($latestUsedSpaces / $maxSpaces) * 100;  // 區域使用率
$totalUsageRate = ($latestUsedSpaces / $totalSpaces) * 100; // 總使用率

// 回傳成功數據
echo json_encode([
    'success' => true,
    'data' => [
        'zoneColor' => $zoneColor,
        'latestUsedSpaces' => $latestUsedSpaces,
        'maxSpaces' => $maxSpaces,
        'zoneUsageRate' => round($zoneUsageRate, 2),
        'totalSpaces' => $totalSpaces,
        'totalUsageRate' => round($totalUsageRate, 2),
        'records' => $data
    ]
]);
exit;
?>
