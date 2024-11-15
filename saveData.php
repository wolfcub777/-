<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 從請求中獲取資料
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data)) {
    foreach ($data as $user) {
        $帳號 = $user['userID'];
        $名字 = $user['name'];
        $身份 = $user['identity'];
        $聯絡資訊 = $user['contactInfo'];
        $密碼 = $user['password'];
        $車牌號碼 = $user['plateNumber'];
        $車輛類型 = $user['vehicleType']; // 新增的車輛類型

        // 插入數據到資料庫
        $sql = "INSERT INTO 使用者 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車牌號碼, 車輛類型) 
                VALUES ('$帳號', '$名字', '$身份', '$聯絡資訊', '$密碼', '$車牌號碼', '$車輛類型')";

        if ($conn->query($sql) === TRUE) {
            echo "新記錄創建成功";
        } else {
            echo "錯誤: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    echo "無數據";
}

$conn->close();
?>

