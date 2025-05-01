<?php
header("Content-Type: application/json");
include 'database.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT 名稱, 總車位, 圖片, (SELECT SUM(已停車) FROM 停車區域 WHERE id = ?) AS 已停車 FROM 停車場區域 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $row["剩餘車位"] = $row["總車位"] - $row["已停車"];
    $row["使用率"] = round(($row["已停車"] / $row["總車位"]) * 100, 2);
    echo json_encode(["success" => true, "parking" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "未找到該停車場"]);
}

$stmt->close();
$conn->close();
?>
