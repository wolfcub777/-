<?php
// 資料庫連線參數
$host = 'localhost';
$dbname = '停車場巡查使用系統';
$username = 'root'; // 替換為您的資料庫使用者名稱
$password = ''; // 替換為您的資料庫密碼

try {
    // 建立 PDO 連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '<script>alert("資料庫連線失敗: ' . $e->getMessage() . '");</script>';
    exit;
}

// 接收表單傳遞的帳號和密碼
$account = $_POST['account'];
$password = $_POST['password'];

// 查詢資料庫是否有匹配的帳號和密碼
$sql = "SELECT 密碼, 身份 FROM 使用者 WHERE 帳號 = :account";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':account', $account);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $password === $user['密碼']) {
    // 登入成功，根據身份跳轉
    switch ($user['身份']) {
        case '學生':
        case '教職員':
            header('Location: 學生.HTML');
            break;
        case '總務處':
            header('Location: 總務處.HTML');
            break;
        case '學務處':
            header('Location: 學務處.HTML');
            break;
        case '巡查員':
            header('Location: 巡查員.HTML');
            break;
        case '管理員':
            header('Location: 合體巨獸.HTML');
            break;
        default:
            echo '<script>alert("無效的身份！");</script>';
            exit;
    }
    exit; // 確保跳轉後不執行後續代碼
} else {
    // 登入失敗，顯示彈窗提示
    echo '<script>alert("帳號或密碼錯誤！"); window.location.href = "芝麻開門.HTML";</script>';
}
?>
