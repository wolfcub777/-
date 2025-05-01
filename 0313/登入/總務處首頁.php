<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['科別'] != '總務處') {
    header("Location: 教職員登入.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>總務處首頁</title>
</head>
<body>
    <h2>歡迎, 總務處人員 <?php echo $_SESSION['user']['姓名']; ?></h2>
    <ul>
        <li><a href="管理停車場.php">管理停車場區域</a></li>
        <li><a href="教職員首頁.php">返回教職員首頁</a></li>
        <li><a href="登出.php">登出</a></li>
    </ul>
</body>
</html>
