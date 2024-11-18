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

// 獲取 POST 請求中的公告資料
$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'];
$content = $data['content'];

if (!empty($title) && !empty($content)) {
    // 將所有已標記為最新的公告標示為非最新
    $sqlUpdateOld = "UPDATE 公告 SET 是否最新 = 0 WHERE 是否最新 = 1";
    $conn->query($sqlUpdateOld);

    // 新增新公告並標示為最新公告
    $sql = "INSERT INTO 公告 (標題, 內容, 是否最新) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        echo "公告已成功發布並標示為最新";
    } else {
        echo "發布失敗: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "公告標題與內容不得為空";
}

$conn->close();
?>
