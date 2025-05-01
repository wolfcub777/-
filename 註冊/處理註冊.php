<?php
include 'db_connection.php'; // 連接資料庫

// 取得表單資料
$user_id = $_POST['user_id'] ?? null;
$identity = $_POST['identity'] ?? null;
$name = $_POST['name'] ?? null;
$phone = $_POST['phone'] ?? null;
$email = $_POST['email'] ?? null;
$id_card = $_POST['id_card'] ?? null;
$license_plate = $_POST['license_plate'] ?? null;

// 根據身分填入對應的值
$education_type = ($identity == "學生" || $identity == "教師") ? ($_POST['education_type'] ?? null) : null;
$major = ($identity == "學生" || $identity == "教師") ? ($_POST['major'] ?? null) : null;
$school_time = ($identity == "學生") ? ($_POST['school_time'] ?? null) : null;
$division = ($identity == "職員") ? ($_POST['division'] ?? null) : null;

// 檢查身分證格式
if (!preg_match("/^[A-Z]{1}[0-9]{9}$/", $id_card)) {
    showMessage("❌ 身分證格式錯誤！(A123456789)", "註冊.html", "red");
    exit;
}

// 檢查車牌號碼格式
if (!preg_match("/^[A-Z]{3}-[0-9]{4}$/", $license_plate)) {
    showMessage("❌ 車牌號碼格式錯誤！(ABC-1234)", "註冊.html", "red");
    exit;
}

// 上傳圖片 (如果有上傳)
$image = null;
if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $image = $upload_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image);
}

// 依照身份類別設定不同的 SQL 插入語句
if ($identity === "學生") {
    $sql = "INSERT INTO `帳戶註冊` (學號, 身分, 學部, 科別, 學制, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
} elseif ($identity === "教師") {
    $sql = "INSERT INTO `帳戶註冊` (學號, 身分, 學部, 科別, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
} elseif ($identity === "職員") {
    $sql = "INSERT INTO `帳戶註冊` (學號, 身分, 處室, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
} else {
    showMessage("❌ 身份類型錯誤！", "註冊.html", "red");
    exit;
}

// 準備 SQL 語句
$stmt = $conn->prepare($sql);
if (!$stmt) {
    showMessage("❌ SQL 準備錯誤：" . $conn->error, "註冊.html", "red");
    exit;
}

// 根據身分別，綁定適當的參數
if ($identity === "學生") {
    $stmt->bind_param("sssssssssss", $user_id, $identity, $education_type, $major, $school_time, $name, $phone, $email, $id_card, $license_plate, $image);
} elseif ($identity === "教師") {
    $stmt->bind_param("ssssssssss", $user_id, $identity, $education_type, $major, $name, $phone, $email, $id_card, $license_plate, $image);
} elseif ($identity === "職員") {
    $stmt->bind_param("sssssssss", $user_id, $identity, $division, $name, $phone, $email, $id_card, $license_plate, $image);
}

// 執行 SQL
if ($stmt->execute()) {
    showMessage("✅ 註冊成功！3 秒後跳轉到登入頁面...", "登入.html", "green");
} else {
    showMessage("❌ 註冊失敗：" . $stmt->error, "註冊.html", "red");
}

// 關閉連線
$stmt->close();
$conn->close();

// 顯示訊息框函數
function showMessage($message, $redirect, $color) {
    echo "
    <html>
    <head>
        <meta http-equiv='refresh' content='3;url=$redirect'>
        <link rel='stylesheet' href='styles.css'>
        <style>
            .message {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                padding: 20px;
                background-color: $color;
                color: white;
                font-size: 20px;
                text-align: center;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            }
        </style>
    </head>
    <body>
        <div class='message'>$message</div>
    </body>
    </html>
    ";
    exit;
}
?>
