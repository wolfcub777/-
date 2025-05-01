<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=停車場巡查使用系統;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    $parking_id = isset($_GET['parking_id']) ? intval($_GET['parking_id']) : null;

    if (!$parking_id) {
        echo json_encode(["success" => false, "message" => "未指定停車場"]);
        exit;
    }

    // 查詢該停車場的統計數據
    $stmt = $pdo->prepare("
        SELECT id, 已停車, 送出時間 
        FROM 停車區域 
        WHERE DATE(送出時間) = :date 
        AND 備註 = (SELECT 名稱 FROM 停車場區域 WHERE id = :parking_id) 
        ORDER BY 送出時間 DESC
    ");
    $stmt->execute([
        ":date" => $date,
        ":parking_id" => $parking_id
    ]);

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "records" => $records]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "資料庫錯誤：" . $e->getMessage()]);
}
?>
