<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $學號 = $_POST["user_id"];
    $身分 = ($_POST["identity"] === "教師" || $_POST["identity"] === "職員") ? "教職員" : $_POST["identity"];
    $姓名 = $_POST["name"];
    $電話 = $_POST["phone"];
    $信箱 = $_POST["email"];
    $身分證 = $_POST["id_card"];
    $車牌號碼 = $_POST["license_plate"];

    // 根據身份選擇對應的資料
  $學部 = ($_POST["identity"] === "學生" || $_POST["identity"] === "教職員") ? $_POST["education_type"] : null;
$科別 = ($_POST["identity"] === "學生" || ($_POST["identity"] === "教職員" && $_POST["role"] === "教師")) ? $_POST["major"] : null;
$學制 = ($_POST["identity"] === "學生") ? $_POST["school_time"] : null;
$處室 = ($_POST["identity"] === "教職員" && $_POST["role"] === "職員") ? $_POST["division"] : null;

    // 上傳圖片處理
    $圖片 = null;
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $圖片 = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $圖片);
    }

    // 插入資料到 `帳戶註冊`
    $sql = "INSERT INTO `帳戶註冊` (學號, 身分, 學部, 科別, 學制, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("❌ SQL 錯誤：" . $conn->error);
    }

    $stmt->bind_param("sssssssssss", $學號, $身分, $學部, $科別, $學制, $姓名, $電話, $信箱, $身分證, $車牌號碼, $圖片);

    if ($stmt->execute()) {
        echo "
        <html>
        <head>
            <meta http-equiv='refresh' content='3;url=登入.html'>
            <link rel='stylesheet' href='styles.css'>
            <style>
                .message {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    padding: 20px;
                    background-color: #4CAF50;
                    color: white;
                    font-size: 20px;
                    text-align: center;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
                }
            </style>
        </head>
        <body>
            <div class='message'>
                ✅ 註冊成功！3 秒後跳轉到登入頁面...
            </div>
        </body>
        </html>
        ";
    } else {
        echo "
        <html>
        <head>
            <meta http-equiv='refresh' content='3;url=註冊.html'>
            <link rel='stylesheet' href='styles.css'>
            <style>
                .message {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    padding: 20px;
                    background-color: #f44336;
                    color: white;
                    font-size: 20px;
                    text-align: center;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
                }
            </style>
        </head>
        <body>
            <div class='message'>
                ❌ 註冊失敗，請重試！3 秒後跳回註冊頁面...
            </div>
        </body>
        </html>
        ";
    }

    $stmt->close();
}

$conn->close();
?>
