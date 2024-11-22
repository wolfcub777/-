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

// 檢查是否收到 POST 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
    $content = isset($_POST['content']) ? $conn->real_escape_string($_POST['content']) : '';
    $image_path = null;

    // 確保標題和內容不為空
    if (!empty($title) && !empty($content)) {
        // 處理圖片上傳
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'uploads/images/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true); // 創建目錄
            }

            $image_name = basename($_FILES['image']['name']);
            $target_file = $upload_dir . $image_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // 驗證圖片類型
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($image_file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = $target_file;
                } else {
                    echo "圖片上傳失敗";
                    $conn->close();
                    exit;
                }
            } else {
                echo "僅支援 JPG、JPEG、PNG 和 GIF 格式的圖片";
                $conn->close();
                exit;
            }
        }

        // 將所有已標記為最新的公告設為非最新
        $sqlUpdateOld = "UPDATE 公告 SET 是否最新 = 0 WHERE 是否最新 = 1";
        $conn->query($sqlUpdateOld);

        // 新增新公告並標示為最新公告
        $sql = "INSERT INTO 公告 (標題, 內容, 是否最新, image_path) VALUES (?, ?, 1, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $content, $image_path);

        if ($stmt->execute()) {
            echo "公告已成功發布並標示為最新";
        } else {
            echo "發布失敗: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "公告標題與內容不得為空";
    }
} else {
    echo "僅接受 POST 請求";
}

$conn->close();
?>
