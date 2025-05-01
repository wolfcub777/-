<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "停車場巡查使用系統");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "資料庫連接失敗"]));
}

$id = $_POST['id'];
$newValue = $_POST['newValue'];

$sql = "UPDATE 停車區域 SET `已停車` = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $newValue, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "更新成功"]);
} else {
    echo json_encode(["success" => false, "message" => "更新失敗"]);
}

$stmt->close();
$conn->close();
?>


