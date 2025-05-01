<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=停車場巡查使用系統;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['usedSpots']) || !is_numeric($data['usedSpots'])) {
        echo json_encode(["success" => false, "message" => "無效的輸入"]);
        exit;
    }

    // 插入新的巡邏記錄，而不是累加
    $stmt = $pdo->prepare("INSERT INTO 停車區域 (總車位, 已停車, 送出時間) VALUES (:total, :used, NOW())");
    $stmt->execute([
        ":total" => 454,
        ":used" => intval($data['usedSpots'])
    ]);

    echo json_encode(["success" => true, "message" => "數據提交成功"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "資料庫錯誤：" . $e->getMessage()]);
}
?>
