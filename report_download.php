<?php
// 如果有 AJAX 請求，執行資料庫查詢並返回 JSON 資料
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    ob_start();

    // 設定資料庫連線
    $servername = "localhost";
    $username = "root"; // 根據您的資料庫設定更改
    $password = ""; // 根據您的資料庫設定更改
    $dbname = "停車場巡查使用系統"; // 根據您的實際資料庫名稱更改

    // 建立連線
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 檢查連線
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        ob_end_flush();
        exit;
    }

    // 查詢 `審核結果` 資料表只抓取 `車輛類型` 欄位
    $sql = "SELECT 車輛類型 FROM 審核結果 WHERE 車輛類型 IN ('機車', '汽車')";
    $result = $conn->query($sql);

    $data = array();

    // 檢查是否有資料
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        error_log("查詢無結果或資料表為空"); // 除錯訊息
        http_response_code(204);
        echo json_encode(["message" => "No data found"]);
        ob_end_flush();
        $conn->close();
        exit;
    }

    // 清除輸出緩衝，保證沒有其他輸出干擾
    ob_end_clean();

    // 回傳完整的 JSON 資料
    echo json_encode($data);

    // 關閉連線
    $conn->close();
    exit; // 確保程式結束後不會有額外的輸出
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>下載車輛類型報表</title>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>下載車輛類型報表</h1>
    <button onclick="fetchVehicleTypes()" class="button">下載報表</button>

    <script>
        async function fetchVehicleTypes() {
            try {
                const response = await fetch('report_download.php', {
                    method: 'POST'
                });
                const data = await response.json();

                // 檢查 JSON 資料是否有效
                if (!data || data.length === 0 || data.message) {
                    console.error('No data found');
                    alert('無法下載報表，因為沒有找到資料');
                    return;
                }

                // 準備 Excel 資料
                const headers = ['序號', '車輛類型'];
                const excelData = [headers, ...data.map((item, index) => [
                    index + 1,
                    item.車輛類型 || ''
                ])];

                // 生成 Excel 檔案
                const worksheet = XLSX.utils.aoa_to_sheet(excelData);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "總報表");

                // 下載 Excel 檔案
                XLSX.writeFile(workbook, "總報表.xlsx");
            } catch (error) {
                console.error('Error fetching vehicle data:', error);
                alert('下載報表時發生錯誤，請檢查錯誤日誌');
            }
        }
    </script>
</body>
</html>
