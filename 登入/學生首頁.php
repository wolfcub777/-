<?php
session_start();
include 'db_connection.php';

// 檢查是否已登入
if (!isset($_SESSION['account']) || !isset($_SESSION['identity']) || $_SESSION['identity'] !== '學生') {
    header("Location: 學生登入.html");
    exit();
}

// 取得學生資料
$account = $_SESSION['account'];
$sql = "SELECT * FROM `新版使用者` WHERE 學號 = ? AND 身分 = '學生'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 登出
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: 學生登入.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學生首頁</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { margin-top: 50px; }
        button { padding: 10px 20px; margin-top: 20px; font-size: 16px; cursor: pointer; }
        .logout { background-color: red; color: white; border: none; border-radius: 5px; }
        .logout:hover { background-color: darkred; }
    </style>
</head>
<body>
    <div class="container">
        <h2>歡迎, <?php echo $user['姓名']; ?>！</h2>
        <p>學號：<?php echo $user['學號']; ?></p>
        <p>科別：<?php echo $user['科別']; ?></p>
        <p>學制：<?php echo $user['學制']; ?></p>
        <p>信箱：<?php echo $user['信箱']; ?></p>
        <form method="post">
            <button type="submit" name="logout" class="logout">登出</button>
        </form>
    </div>
</body>
</html>
