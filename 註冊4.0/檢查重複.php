<?php
include 'db_connection.php';

$user_id = $_POST['user_id'] ?? '';
$email = $_POST['email'] ?? '';

$response = ["exists" => false, "message" => ""];

// 檢查 `新版使用者` 和 `帳戶註冊` 是否已存在該學號或信箱
$check_sql = "SELECT '學號已註冊' AS message FROM `新版使用者` WHERE 學號 = ?
              UNION
              SELECT '學號審核中' AS message FROM `帳戶註冊` WHERE 學號 = ?
              UNION
              SELECT '信箱已註冊' AS message FROM `新版使用者` WHERE 信箱 = ?
              UNION
              SELECT '信箱審核中' AS message FROM `帳戶註冊` WHERE 信箱 = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ssss", $user_id, $user_id, $email, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($row = $check_result->fetch_assoc()) {
    $response["exists"] = true;
    $response["message"] = "❌ " . $row["message"];
}

echo json_encode($response);
?>
