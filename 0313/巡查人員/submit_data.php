<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=停車場巡查使用系統;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    // 檢查前端傳來的數據
    if (!isset($data['usedSpots'], $data['parkingAreaId']) || !is_numeric($data['usedSpots']) || !is_numeric($data['parkingAreaId'])) {
        echo json_encode(["success" => false, "message" => "無效的輸入"]);
        exit;
    }

    $parkingAreaId = intval($data['parkingAreaId']);
    $usedSpots = intval($data['usedSpots']);

    // 查詢停車場資訊
    $stmt = $pdo->prepare("SELECT 名稱, 總車位 FROM 停車場區域 WHERE id = :id");
    $stmt->execute([":id" => $parkingAreaId]);
    $parkingArea = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parkingArea) {
        echo json_encode(["success" => false, "message" => "找不到該停車場"]);
        exit;
    }

    $totalSpots = intval($parkingArea['總車位']);
    $parkingName = $parkingArea['名稱'];
    $remainingSpots = $totalSpots - $usedSpots;

    if ($remainingSpots < 0) {
        echo json_encode(["success" => false, "message" => "已停車數量超過總車位"]);
        exit;
    }

    // 插入新的巡查記錄
    $stmt = $pdo->prepare("INSERT INTO 停車區域 (總車位, 已停車, 剩餘車位, 使用率, 備註, 送出時間) 
                           VALUES (:total, :used, :remaining, :usageRate, :remark, NOW())");
    $stmt->execute([
        ":total" => $totalSpots,
        ":used" => $usedSpots,
        ":remaining" => $remainingSpots,
        ":usageRate" => round(($usedSpots / $totalSpots) * 100, 2),
        ":remark" => $parkingName
    ]);

    echo json_encode(["success" => true, "message" => "數據提交成功"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "資料庫錯誤：" . $e->getMessage()]);
}
?>
