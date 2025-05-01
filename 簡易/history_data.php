<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=停車場巡查使用系統;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

    // 查詢當日所有巡邏記錄
    $stmt = $pdo->prepare("SELECT id, 已停車, 送出時間 FROM 停車區域 WHERE DATE(送出時間) = :date ORDER BY 送出時間 DESC");
    $stmt->execute([":date" => $date]);

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "records" => $records]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "資料庫錯誤：" . $e->getMessage()]);
}
?>
