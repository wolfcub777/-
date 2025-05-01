<?php
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=parking_report.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // 防止 Excel 亂碼

// 連接資料庫
$servername = "localhost";
$username = "root"; // 根據你的 MySQL 設定
$password = "";
$dbname = "停車場巡查使用系統"; // 你的資料庫名稱

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

$output = fopen("php://output", "w");

// **寫入每月統計標題**
fputcsv($output, ["月份", "平均已停車", "平均剩餘車位", "平均使用率 (%)"]);

// SQL 查詢每個月的統計數據
$sql_month = "SELECT 
                DATE_FORMAT(送出時間, '%c') AS 月份,
                ROUND(AVG(已停車)) AS 平均已停車,
                ROUND(AVG(剩餘車位)) AS 平均剩餘車位,
                ROUND(AVG(使用率), 2) AS 平均使用率
              FROM 停車區域
              GROUP BY 月份
              ORDER BY 月份 DESC";

$result_month = $conn->query($sql_month);

// **寫入每月統計數據**
while ($row = $result_month->fetch_assoc()) {
    fputcsv($output, [$row['月份'] . "月", $row['平均已停車'], $row['平均剩餘車位'], $row['平均使用率'] . "%"]);
}

// **分隔行，讓週數統計有區別**
fputcsv($output, []); 

// **寫入每週統計標題**
fputcsv($output, ["月份", "週數", "平均已停車", "平均剩餘車位", "平均使用率 (%)"]);

// SQL 查詢每週的統計數據
$sql_week = "SELECT 
                DATE_FORMAT(送出時間, '%c') AS 月份, 
                CEIL(DAY(送出時間) / 7) AS 週數,  -- 改成強制 1~7 為第一週，8~14 為第二週...
                ROUND(AVG(已停車)) AS 平均已停車,
                ROUND(AVG(剩餘車位)) AS 平均剩餘車位,
                ROUND(AVG(使用率), 2) AS 平均使用率
              FROM 停車區域
              GROUP BY 月份, 週數
              ORDER BY 月份 DESC, 週數 ASC";

$result_week = $conn->query($sql_week);

// **寫入每週統計數據**
while ($row = $result_week->fetch_assoc()) {
    fputcsv($output, [$row['月份'] . "月", "第" . $row['週數'] . "週", $row['平均已停車'], $row['平均剩餘車位'], $row['平均使用率'] . "%"]);
}

fclose($output);
$conn->close();
exit();
