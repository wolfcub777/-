<?php
// 檔名: import_to_database.php

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
    ob_end_clean(); // 清除編程區避免多餘輸出
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// 自動建立資料表如果存在
$createTableSql = "
    CREATE TABLE IF NOT EXISTS 總報表 (
        序號 INT NOT NULL,
        姓名 VARCHAR(100) NOT NULL,
        車輛類型 VARCHAR(50) NOT NULL,
        聯絡電話 VARCHAR(100) NOT NULL,
        車牌號碼 VARCHAR(20) NOT NULL,
        出場時間 VARCHAR(50),
        學號 VARCHAR(50),
        標記 VARCHAR(50),
        PRIMARY KEY (序號)
    )
";

if (!$conn->query($createTableSql)) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(["success" => false, "error" => "Failed to create table: " . $conn->error]);
    $conn->close();
    exit;
}

// 從請求中取得數據
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['reportData']) || !is_array($input['reportData'])) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(["success" => false, "error" => "Invalid input data"]);
    $conn->close();
    exit;
}

$reportData = $input['reportData'];

// 將資料匯入到資料庫
$stmt = $conn->prepare("INSERT INTO 總報表 (序號, 姓名, 車輛類型, 聯絡電話, 車牌號碼, 出場時間, 學號, 標記) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(["success" => false, "error" => "Failed to prepare statement: " . $conn->error]);
    $conn->close();
    exit;
}

// 不提供第一行的標題
for ($i = 1; $i < count($reportData); $i++) {
    $row = $reportData[$i];

    $stmt->bind_param("isssssss", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
    if (!$stmt->execute()) {
        error_log("Failed to insert row: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();

// 回應成功訊息
ob_end_clean();
echo json_encode(["success" => true]);
exit;
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>格式化資料庫查詢</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <h1>選擇日期來查詢資料</h1>
    <input type="date" id="reportDate">
    <button id="fetchButton">查詢總報表資料</button>
    <table id="resultTable" border="1">
        <thead>
            <tr>
                <th>序號</th>
                <th>姓名</th>
                <th>車輛類型</th>
                <th>聯絡電話</th>
                <th>車牌號碼</th>
                <th>出場時間</th>
                <th>學號</th>
                <th>標記</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        document.getElementById('fetchButton').addEventListener('click', function() {
            const reportDate = document.getElementById('reportDate').value;
            if (!reportDate) {
                alert('請選擇日期');
                return;
            }

            fetch('fetch_report_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ date: reportDate })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const tableBody = document.querySelector('#resultTable tbody');
                    tableBody.innerHTML = '';
                    result.data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${row['序號']}</td><td>${row['姓名']}</td><td>${row['車輛類型']}</td><td>${row['聯絡電話']}</td><td>${row['車牌號碼']}</td><td>${row['出場時間']}</td><td>${row['學號']}</td><td>${row['標記']}</td>`;
                        tableBody.appendChild(tr);
                    });
                } else {
                    alert('查詢失敗: ' + result.error);
                }
            })
            .catch(error => {
                alert('發生錯誤: ' + error);
            });
        });
    </script>
</body>
</html>
