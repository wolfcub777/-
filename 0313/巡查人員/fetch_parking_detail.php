<?php
include 'database.php';

header("Content-Type: application/json");

// 檢查是否有提供 `parking_id`
if (!isset($_GET['parking_id'])) {
    echo json_encode(["success" => false, "message" => "缺少停車場 ID"]);
    exit;
}

$parking_id = intval($_GET['parking_id']); // 確保 ID 是數字

$stmt = $conn->prepare("SELECT 名稱, 總車位, 圖片 FROM 停車場區域 WHERE id = ?");
$stmt->bind_param("i", $parking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "name" => $row['名稱'],
        "total_spots" => $row['總車位'],
        "image" => $row['圖片']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "找不到該停車場"]);
}

$stmt->close();
$conn->close();
?>
