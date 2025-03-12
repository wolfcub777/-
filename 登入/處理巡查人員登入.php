<?php
include 'db_connection.php';

$account = $_POST['account'];
$password = $_POST['password'];

$sql = "SELECT * FROM `新版使用者` WHERE 學號 = ? AND 身分證 = ? AND 身分 = '巡查人員'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("❌ SQL 準備失敗：" . $conn->error);
}
$stmt->bind_param("ss", $account, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: 巡查人員首頁.php");
} else {
    echo "<script>alert('❌ 登入失敗，請檢查帳號或密碼'); window.location.href = '巡查人員登入.html';</script>";
}

$stmt->close();
$conn->close();
?>
