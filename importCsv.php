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

if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // 跳過 CSV 檔案的第一行標題
    fgetcsv($csvFile);

    // 讀取每一行資料
    while (($row = fgetcsv($csvFile)) !== FALSE) {
        $帳號 = $row[0];
        $名字 = $row[1];
        $身份 = $row[2];
        $聯絡資訊 = $row[3];
        $密碼 = $row[4];
        $車牌號碼 = $row[5];
        $車輛類型 = $row[6];

        // 插入到資料庫中
        $sql = "INSERT INTO 使用者 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車牌號碼, 車輛類型) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $帳號, $名字, $身份, $聯絡資訊, $密碼, $車牌號碼, $車輛類型);
        $stmt->execute();
    }

    fclose($csvFile);
    echo "資料匯入成功！";
    header("Location: 輸入.HTML"); // 匯入成功後返回輸入頁面
} else {
    echo "請選擇一個有效的 CSV 檔案進行上傳。";
}

$conn->close();
?>

