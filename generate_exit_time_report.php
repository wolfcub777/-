<?php 
// 檔名: generate_exit_time_report.php

// 防止未預期的輸出
ob_start();
header('Content-Type: text/html; charset=utf-8');

// 設定資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// 查詢機車合格使用車牌
$sql_motorcycles = "
    SELECT l.名字, '機車' AS 車輛類型, l.聯絡資訊, l.車牌號碼, p.exit_time, p.id_number, p.marked
    FROM `機車合格使用車牌` AS l
    LEFT JOIN parking_entries AS p ON l.車牌號碼 = p.license_plate
";

// 查詢汽車合格使用車牌
$sql_cars = "
    SELECT l.名字, '汽車' AS 車輛類型, l.聯絡資訊, l.車牌號碼, p.exit_time, p.id_number, p.marked
    FROM `汽車合格使用車牌` AS l
    LEFT JOIN parking_entries AS p ON l.車牌號碼 = p.license_plate
";

// 執行查詢並合併結果
$result_motorcycles = $conn->query($sql_motorcycles);
$result_cars = $conn->query($sql_cars);

$data = [
    ["序號", "姓名", "車輛類型", "聯絡電話", "車牌號碼", "出場時間", "學號/教職員編號", "標記"]
];

$index = 1;

// 處理機車合格使用車牌的結果
if ($result_motorcycles && $result_motorcycles->num_rows > 0) {
    while ($row = $result_motorcycles->fetch_assoc()) {
        $data[] = [
            $index++, 
            $row["名字"], 
            $row["車輛類型"], 
            $row["聯絡資訊"], 
            $row["車牌號碼"], 
            $row["exit_time"] ?? '無', 
            $row["id_number"] ?? '無', 
            $row["marked"] ?? '無'
        ];
    }
}

// 處理汽車合格使用車牌的結果
if ($result_cars && $result_cars->num_rows > 0) {
    while ($row = $result_cars->fetch_assoc()) {
        $data[] = [
            $index++, 
            $row["名字"], 
            $row["車輛類型"], 
            $row["聯絡資訊"], 
            $row["車牌號碼"], 
            $row["exit_time"] ?? '無', 
            $row["id_number"] ?? '無', 
            $row["marked"] ?? '無'
        ];
    }
}

// 如果無結果
if (count($data) === 1) {
    error_log("查詢無結果或資料表為空");
    http_response_code(204);
    ob_end_clean();
    echo json_encode(["message" => "No data found"]);
    $conn->close();
    exit;
}

ob_end_clean();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>匯出總報表</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e7f5e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h1 {
            text-align: center;
            color: #2b4f36;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            background-color: #d6f3d3;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        button, .button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        button:hover, .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="button-container">
        <h1>停車場巡查使用系統 - 總報表匯出</h1>
        <button id="exportButton">匯出總報表</button>
        <button id="importButton">將資料匯入資料庫</button>
        <a href="合體巨獸.html" class="button">返回</a>
    </div>

    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            const data = <?php echo json_encode($data); ?>;
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, '總報表');
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
$conn->close();
?>
