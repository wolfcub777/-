<?php
session_start();
include 'db_connection.php'; // 連接資料庫

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = trim($_POST['account']); // 移除空格
    $password = trim($_POST['password']); // 移除空格

    // **檢查輸入是否為空**
    if (empty($account) || empty($password)) {
        echo "<script>alert('❌ 帳號或密碼不可為空！'); window.location.href = '教職員登入.html';</script>";
        exit();
    }

    // **SQL 查詢**
    $sql = "SELECT * FROM `新版使用者` WHERE `學號` = ? AND `身分證` = ? AND (`身分` = '教師' OR `身分` = '職員')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("❌ SQL 準備失敗：" . $conn->error);
    }

    $stmt->bind_param("ss", $account, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // **登入成功處理**
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['account'] = $user['學號'];
        $_SESSION['identity'] = $user['身分'];
        $_SESSION['name'] = $user['姓名'];

        header("Location: 教職員首頁.php");
        exit();
    } else {
        echo "<script>alert('❌ 登入失敗，請檢查學號與身分證是否正確！'); window.location.href = '教職員登入.html';</script>";
        exit();
    }

    $stmt->close();
}
$conn->close();
?>
