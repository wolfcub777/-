<?php
// 設置正確的 Content-Type 標頭
header('Content-Type: application/json');

// 從輸入流中讀取 JSON 格式的資料
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // 建立與資料庫的連接
    $servername = "localhost"; // 根據實際情況更改
    $username = "root";        // 根據實際情況更改
    $password = "";            // 根據實際情況更改
    $dbname = "your_database_name"; // 根據實際情況更改

    // 建立資料庫連線
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 檢查連接是否成功
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['message' => '資料庫連接失敗：' . $conn->connect_error, 'status' => 'error']);
        exit();
    }

    // 處理插入資料的邏輯
    foreach ($data as $period => $parkingSpaces) {
        foreach ($parkingSpaces as $space) {
            $id = $space['id'];
            $description = $conn->real_escape_string($space['description']);
            $status = $conn->real_escape_string($space['status']);
            $lastModified = $conn->real_escape_string($space['lastModified']);

            // 插入或更新資料的 SQL 語句
            $sql = "INSERT INTO parking_table (id, description, status, last_modified)
                    VALUES ('$id', '$description', '$status', '$lastModified')
                    ON DUPLICATE KEY UPDATE 
                    description = '$description', status = '$status', last_modified = '$lastModified'";

            if (!$conn->query($sql)) {
                http_response_code(500);
                echo json_encode(['message' => '資料插入失敗：' . $conn->error, 'status' => 'error']);
                $conn->close();
                exit();
            }
        }
    }

    // 成功回應
    echo json_encode(['message' => '資料已成功匯入！', 'status' => 'success']);
    $conn->close();
} else {
    // 如果未接收到資料或格式錯誤，返回 400 狀態碼
    http_response_code(400);
    echo json_encode(['message' => '無效的資料格式或未接收到資料', 'status' => 'error']);
}
?>
