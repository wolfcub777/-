<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 確認帳號參數是否存在
if (isset($_GET['account'])) {
    $account = $_GET['account'];

    // 使用預備語句來執行刪除操作，避免 SQL 注入
    $sql = "DELETE FROM 使用者 WHERE 帳號 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $account);

    if ($stmt->execute()) {
        // 刪除成功後重定向回歷史紀錄頁面
        header("Location: history.php"); // 將此行的 "history.php" 替換為您的歷史紀錄頁面名稱
        exit();
    } else {
        echo "刪除失敗: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "無效的請求";
}

$conn->close();
?>
