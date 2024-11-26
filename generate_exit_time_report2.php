<?php 
// 檔名: generate_exit_time_report.php

// 防止未預期的輸出
ob_start();
header('Content-Type: text/html; charset=utf-8');

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
    ob_end_clean(); // 清除編程區避免多餘輸出
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// 查詢 `審核結果` 資料表，抓取需要的欄位並結合 `parking_entries` 以獲取 `出場時間`，`id_number` 和 `marked`
$sql = "
    SELECT l.名字, l.車輛類型, l.聯絡資訊, l.車牌號碼, p.exit_time, p.id_number, p.marked
    FROM `審核結果` AS l
    LEFT JOIN parking_entries AS p ON l.車牌號碼 = p.license_plate
    WHERE l.車輛類型 IN ('機車', '汽車')
";
$result = $conn->query($sql);

$data = [
    ["序號", "姓名", "車輛類型", "聯絡電話", "車牌號碼", "出場時間", "學號/教職員編號", "標記"]
];

// 檢查是否有資料
if ($result && $result->num_rows > 0) {
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $index++,                  // 序號
            $row["名字"],              // 姓名
            $row["車輛類型"],          // 車輛類型
            $row["聯絡資訊"],          // 聯絡電話
            $row["車牌號碼"],          // 車牌號碼
            $row["exit_time"] ?? '無', // 出場時間（若無則顯示 "無"）
            $row["id_number"],         // 學號/教職員編號
            $row["marked"]             // 標記
        ];
    }
} else {
    // 如果資料表為空或沒有查詢到資料
    error_log("查詢無結果或資料表為空");
    http_response_code(204);
    ob_end_clean(); // 清除編程區避免多餘輸出
    echo json_encode(["message" => "No data found"]);
    $conn->close();
    exit;
}

// 清除輸出編程，保證沒有其他輸出干擾
ob_end_clean();

?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>匯出總報表</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <h1>停車場管理系統 - 總報表匯出</h1>
    <button id="exportButton">匯出總報表</button>
    <button id="importButton">將資料匯入資料庫</button>
    <a href="總務處.HTML" class="button" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #354b72; color: #ffffff; text-decoration: none; border-radius: 5px;">返回</a>

    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            const data = <?php echo json_encode($data); ?>;
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, '總報表');

            // 匯出 Excel 檔案
            XLSX.writeFile(workbook, '總報表.xlsx');
        });

        document.getElementById('importButton').addEventListener('click', function() {
            const data = <?php echo json_encode($data); ?>;
            fetch('import_to_database.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reportData: data })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('資料已成功匯入資料庫');
                } else {
                    alert('匯入失敗: ' + result.error);
                }
            })
            .catch(error => {
                alert('發生錯誤: ' + error);
            });
        });
    </script>
</body>
</html>

<?php
// 關閉連線
$conn->close();
exit;
?>
