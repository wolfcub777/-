<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "停車場巡查使用系統");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "資料庫連接失敗"]));
}

$id = $_POST['id'];

$sql = "DELETE FROM 停車區域 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "刪除成功"]);
} else {
    echo json_encode(["success" => false, "message" => "刪除失敗"]);
}

$stmt->close();
$conn->close();
?>
