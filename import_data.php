<?php 
// 資料庫連接設定
$host = "localhost";  // 資料庫主機
$dbname = "停車場巡查使用系統";  // 資料庫名稱
$username = "root";  // 資料庫使用者名稱
$password = "";  // 資料庫密碼

// 建立資料庫連接
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}

// 檢查資料表是否存在，若不存在則建立
$tableCreateQuery = "CREATE TABLE IF NOT EXISTS parking_table (
    id INT PRIMARY KEY,
    description VARCHAR(255), 
    status VARCHAR(50),
    last_modified TIMESTAMP
)";
try {
    $pdo->exec($tableCreateQuery);
} catch (PDOException $e) {
    die("資料表創建失敗: " . $e->getMessage());
}

// 接收從前端傳送的 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // 準備插入資料的 SQL 語句
    $stmt = $pdo->prepare("INSERT INTO parking_table (id, description, status, last_modified)
                           VALUES (:id, :description, :status, :last_modified)
                           ON DUPLICATE KEY UPDATE description = :description, status = :status, last_modified = :last_modified");

    // 處理每個停車位資料
    foreach ($data as $period => $spaces) {
        foreach ($spaces as $space) {
            // 檢查 'lastModified' 是否存在，若不存在，給予預設值
            $lastModified = isset($space['lastModified']) ? $space['lastModified'] : date('Y-m-d H:i:s');
            $stmt->execute([
                ':id' => $space['id'],
                ':description' => $space['description'],
                ':status' => $space['status'],
                ':last_modified' => $lastModified
            ]);
        }
    }
    echo "資料匯入成功！";
} else {
    echo "沒有接收到任何資料。";
}
?>
