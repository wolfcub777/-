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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 逐一處理每一行數據
    foreach ($_POST['account'] as $index => $account) {
        $名字 = $_POST['名字'][$index];
        $身份 = $_POST['身份'][$index];
        $聯絡資訊 = $_POST['聯絡資訊'][$index];
        $密碼 = $_POST['密碼'][$index];
        $車牌號碼 = $_POST['車牌號碼'][$index];
        $車輛類型 = $_POST['車輛類型'][$index];

        // 更新資料庫
        $sql = "UPDATE 使用者 SET 名字='$名字', 身份='$身份', 聯絡資訊='$聯絡資訊', 密碼='$密碼', 車牌號碼='$車牌號碼', 車輛類型='$車輛類型' WHERE 帳號='$account'";

        if ($conn->query($sql) !== TRUE) {
            echo "錯誤更新資料: " . $conn->error;
        }
    }
    // 返回到主頁面或顯示成功信息
    header('Location: history.php');
    exit();
}

$conn->close();
?>
