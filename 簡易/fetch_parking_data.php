<?php
header("Content-Type: application/json");

// 連接資料庫
$servername = "localhost";
$username = "root"; // 根據你的 MySQL 設定
$password = "";
$dbname = "停車場巡查使用系統"; // 你的資料庫名稱

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "資料庫連線失敗"]));
}

// 取得查詢參數
$type = isset($_GET['type']) ? $_GET['type'] : 'day';
$value = isset($_GET['value']) ? $_GET['value'] : date('Y-m-d'); // 預設查詢今天

if ($type === 'day') {
    // 查詢特定日期的所有數據
    $sql = "SELECT 已停車, 剩餘車位, 使用率, 送出時間 FROM 停車區域 WHERE DATE(送出時間) = ? ORDER BY 送出時間 DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();

    $records = [];
    $total_usage_rate = 0;
    $total_entries = 0;

    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
        $total_usage_rate += $row['使用率'];
        $total_entries++;
    }

    // 計算平均使用率
    $average_usage_rate = ($total_entries > 0) ? round($total_usage_rate / $total_entries, 2) : 0;

    echo json_encode([
        "success" => true,
        "records" => $records,
        "total_entries" => $total_entries,
        "average_usage_rate" => $average_usage_rate
    ], JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
    $conn->close();
}
?>
