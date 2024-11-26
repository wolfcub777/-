<?php

// 建立資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 接收從前端傳來的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

// 對於每個時段的資料，分別匯入對應的資料表
foreach (['first' => 'c1', 'second' => 'c2', 'third' => 'c3'] as $period => $tableName) {
    if (isset($data[$period])) {
        // 檢查資料表是否存在，若不存在則建立
        $createTableSql = "CREATE TABLE IF NOT EXISTS `$tableName` (
            id INT NOT NULL PRIMARY KEY,
            status VARCHAR(20),
            description VARCHAR(255),
            lastModified VARCHAR(50)
        )";

        if ($conn->query($createTableSql) === FALSE) {
            echo "建立資料表失敗: " . $conn->error;
            exit;
        }

        // 確認 'lastModified' 欄位是否存在，若不存在則新增
        $addColumnSql = "ALTER TABLE `$tableName` ADD COLUMN IF NOT EXISTS `lastModified` VARCHAR(50)";
        if ($conn->query($addColumnSql) === FALSE) {
            echo "新增欄位失敗: " . $conn->error;
            exit;
        }

        foreach ($data[$period] as $space) {
            $id = $space['id'];
            $status = $space['status'];
            $description = $space['description'];
            $lastModified = isset($space['lastModified']) ? $space['lastModified'] : '';

            // 使用 ON DUPLICATE KEY UPDATE 來避免重複插入
            $sql = "INSERT INTO `$tableName` (id, status, description, lastModified) VALUES (?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE status = VALUES(status), description = VALUES(description), lastModified = VALUES(lastModified)";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("isss", $id, $status, $description, $lastModified);
                if ($stmt->execute() === FALSE) {
                    echo "準備插入語句失敗: " . $stmt->error;
                    exit;
                }
                $stmt->close();
            } else {
                echo "SQL 語句準備失敗: " . $conn->error;
                exit;
            }
        }
    }
}

echo "資料匯入成功";
$conn->close();
?>