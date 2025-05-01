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

// 先檢查 `新版使用者` 和 `帳戶註冊` 是否已經有該學號
$check_sql = "SELECT '已註冊' AS status FROM `新版使用者` WHERE 學號 = ?
              UNION
              SELECT '審核中' AS status FROM `帳戶註冊` WHERE 學號 = ?";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    showMessage("❌ SQL 檢查錯誤：" . $conn->error, false);
}
$check_stmt->bind_param("ss", $user_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

// 若查到資料，則返回錯誤
if ($check_result->num_rows > 0) {
    showMessage("❌ 學號 $user_id ($name) 註冊失敗，請確認是否已註冊或審核中", false);
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
    $sql = "INSERT INTO `帳戶註冊` (學號, 身分, 科別, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
} else {
    showMessage("❌ 身份類型錯誤！", false);
}

// 準備 SQL 語句
$stmt = $conn->prepare($sql);
if (!$stmt) {
    showMessage("❌ SQL 準備錯誤：" . $conn->error, false);
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
    showMessage("✅ 學號 $user_id ($name) 註冊成功！", true);
} else {
    showMessage("❌ 學號 $user_id ($name) 註冊失敗：" . $stmt->error, false);
}

// 關閉連線
$stmt->close();
$conn->close();

// **顯示訊息**
function showMessage($message, $success) {
    if ($success) {
        echo "
        <html>
        <head>
            <meta http-equiv='refresh' content='3;url=登入.html'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin-top: 50px;
                }
                .message-box {
                    display: inline-block;
                    background-color: #28a745;
                    color: white;
                    padding: 20px;
                    border-radius: 10px;
                    font-size: 18px;
                }
            </style>
        </head>
        <body>
            <div class='message-box'>$message</div><br>
            <p>3 秒後將自動跳轉到登入頁面...</p>
        </body>
        </html>
        ";
    } else {
        echo "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin-top: 50px;
                }
                .message-box {
                    display: inline-block;
                    background-color: #dc3545;
                    color: white;
                    padding: 20px;
                    border-radius: 10px;
                    font-size: 18px;
                }
                .back-btn {
                    margin-top: 20px;
                    padding: 10px 15px;
                    font-size: 16px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }
                .back-btn:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='message-box'>$message</div><br>
            <button class='back-btn' onclick='window.location.href=\"註冊.html\"'>返回</button>
        </body>
        </html>
        ";
    }
    exit();
}
?>
