<?php
// 防止未預期的輸出
ob_start();
header('Content-Type: application/json; charset=utf-8');

// 設定資料庫連線
$servername = "localhost";
$username = "root"; // 根據您的資料庫設定更改
$password = ""; // 根據您的資料庫設定更改
$dbname = "停車場巡查使用系統"; // 根據您的資料庫名稱更改

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    http_response_code(500);
    ob_end_clean(); // 清除緩衝區避免多餘輸出
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// 查詢 `審核結果` 資料表，抓取需要的欄位
$sql = "SELECT 車輛類型, 車牌號碼, 聯絡資訊, 名字 FROM 審核結果 WHERE 車輛類型 IN ('機車', '汽車')";
$result = $conn->query($sql);

$data = array();

// 檢查是否有資料
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            '序號' => count($data) + 1, // 順序編號
            '姓名' => $row['名字'],
            '車輛類型' => $row['車輛類型'],
            '聯絡電話' => $row['聯絡資訊'],
            '車牌號碼' => $row['車牌號碼']
        );
    }
} else {
    // 如果資料表為空或沒有查詢到資料
    error_log("查詢無結果或資料表為空");
    http_response_code(204);
    ob_end_clean(); // 清除緩衝區避免多餘輸出
    echo json_encode(["message" => "No data found"]);
    $conn->close();
    exit;
}

// 清除輸出緩衝，保證沒有其他輸出干擾
ob_end_clean();

// 回傳完整的 JSON 資料
echo json_encode($data);

// 關閉連線
$conn->close();
exit;
?>
