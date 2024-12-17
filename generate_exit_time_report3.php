<?php 
// 檔名: generate_exit_time_report2.php

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

// 查詢資料
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

if ($result && $result->num_rows > 0) {
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $index++, 
            htmlspecialchars($row["名字"]), 
            htmlspecialchars($row["車輛類型"]),
            htmlspecialchars($row["聯絡資訊"]),
            htmlspecialchars($row["車牌號碼"]),
            $row["exit_time"] ?? '無',
            htmlspecialchars($row["id_number"] ?? '無'),
            htmlspecialchars($row["marked"] ?? '無')
        ];
    }
} else {
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
    <title>總報表匯出</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: 	#5CADAD; /* 淺藍背景 */
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #613030; /* 白色帶藍陰影 */
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #3a8fb7; /* 藍色標題 */
            margin-bottom: 20px;
        }

        .button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            background-color: #3a8fb7; /* 藍色按鈕 */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
        }

        .button:hover {
            background-color: #2c7da0; /* 按鈕 Hover 深藍色 */
            transform: translateY(-2px);
        }

        .back-button {
            display: block;
            margin-top: 20px;
            background-color: #68c4e4; /* 淺藍返回按鈕 */
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #57b8d8;
            transform: translateY(-2px);
        }

        footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>停車場巡查使用系統 - 總報表匯出</h1>
        <button class="button" id="exportButton">匯出總報表</button>
        <button class="button" id="importButton">將資料匯入資料庫</button>
        <a href="學務處.HTML" class="back-button">返回</a>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> 停車場巡查使用系統. All Rights Reserved.
    </footer>

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
                headers: { 'Content-Type': 'application/json' },
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
            .catch(error => alert('發生錯誤: ' + error));
        });
    </script>
</body>
</html>

<?php
$conn->close();
exit;
?>
