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

// 查詢最新公告
$sqlLatest = "SELECT * FROM 公告 WHERE 是否最新 = 1";
$resultLatest = $conn->query($sqlLatest);

// 查詢歷史公告
$sqlHistory = "SELECT * FROM 公告 WHERE 是否最新 = 0 ORDER BY 發布時間 DESC";
$resultHistory = $conn->query($sqlHistory);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看已發布公告</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .announcement {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .announcement h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .announcement p {
            margin: 5px 0;
        }
        .timestamp {
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>最新公告</h1>
        <?php
        if ($resultLatest->num_rows > 0) {
            while($row = $resultLatest->fetch_assoc()) {
                echo "<div class='announcement'>";
                echo "<h2>" . htmlspecialchars($row['標題']) . "</h2>";
                echo "<p class='timestamp'>" . $row['發布時間'] . "</p>";
                echo "<p>" . nl2br(htmlspecialchars($row['內容'])) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>目前沒有最新公告。</p>";
        }
        ?>
    </div>

    <div class="container">
        <h1>歷史公告</h1>
        <?php
        if ($resultHistory->num_rows > 0) {
            while($row = $resultHistory->fetch_assoc()) {
                echo "<div class='announcement'>";
                echo "<h2>" . htmlspecialchars($row['標題']) . "</h2>";
                echo "<p class='timestamp'>" . $row['發布時間'] . "</p>";
                echo "<p>" . nl2br(htmlspecialchars($row['內容'])) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>目前沒有歷史公告。</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
