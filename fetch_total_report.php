<?php
// 檔名: fetch_total_report.php

// 禁止顯示 PHP 錯誤提示，防止 HTML 格式的錯誤消息輸出
ini_set('display_errors', 0);
error_reporting(0);

// 設置資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 設置 Content-Type 為 JSON 格式，保證所有輸出都是 JSON
header('Content-Type: application/json; charset=utf-8');

// 檢查連線
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// 查詢資料表
$sql = "SELECT 序號, 姓名, 車輛類型, 聯絡電話, 車牌號碼, 出場時間, 學號, 標記 FROM 總報表";
$result = $conn->query($sql);

$data = [
    ["序號", "姓名", "車輛類型", "聯絡電話", "車牌號碼", "出場時間", "學號/教職員編號", "標記"]
];

// 獲取資料
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row["序號"],
            $row["姓名"],
            $row["車輛類型"],
            $row["聯絡電話"],
            $row["車牌號碼"],
            $row["出場時間"] ?? '無',
            $row["學號"],
            $row["標記"]
        ];
    }
    echo json_encode($data);
} else {
    echo json_encode(["message" => "No data found"]);
}

// 關閉連線
$conn->close();
exit;
?>
