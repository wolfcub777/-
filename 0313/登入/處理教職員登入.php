<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $_POST['account'];
    $password = $_POST['password'];

    // 檢查帳號密碼
    $stmt = $conn->prepare("SELECT * FROM `新版使用者` WHERE `學號` = ? AND `身分證` = ?");
    $stmt->bind_param("ss", $account, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;

        if ($user['身分'] == '教師') {
            header("Location: 教職員首頁.php");
        } elseif ($user['身分'] == '職員') {
            if ($user['科別'] == '總務處') {
                header("Location: 總務處首頁.php");
            } elseif ($user['科別'] == '學務處') {
                header("Location: 學務處首頁.php");
            } else {
                header("Location: 教職員首頁.php"); // 其他職員預設到教職員首頁
            }
        }
        exit();
    } else {
        echo "<script>alert('登入失敗，請檢查帳號或密碼！'); window.location.href='教職員登入.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
