<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 處理圖片上傳邏輯
function uploadImage($file) {
    $uploadDir = 'uploads/'; // 圖片上傳目錄
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // 確保目錄存在
    }

    $targetFile = $uploadDir . uniqid() . '_' . basename($file['name']); // 使用唯一名稱避免覆蓋
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // 確認是否為圖片
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'message' => '檔案不是有效的圖片'];
    }

    // 確認檔案類型
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return ['success' => false, 'message' => '只允許 JPG、PNG、JPEG、GIF 檔案'];
    }

    // 上傳圖片
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'path' => $targetFile];
    } else {
        return ['success' => false, 'message' => '圖片上傳失敗'];
    }
}

// 接收 AJAX 或多部分表單請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 驗證必要參數
    if (!isset($_POST['id'], $_POST['title'], $_POST['content'])) {
        echo json_encode(["status" => "error", "message" => "缺少必要參數"]);
        http_response_code(400);
        exit;
    }

    $announcementId = $_POST['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $imagePath = null;

    // 處理圖片檔案（如果有上傳圖片）
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = uploadImage($_FILES['image']);
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['path'];
        } else {
            echo json_encode(["status" => "error", "message" => $uploadResult['message']]);
            http_response_code(400);
            exit;
        }
    }

    // 更新公告標題、內容和圖片路徑
    $sql = "UPDATE 公告 SET 標題 = ?, 內容 = ?, image_path = ? WHERE 公告ID = ?";
    $stmt = $conn->prepare($sql);
    if ($imagePath) {
        $stmt->bind_param("sssi", $title, $content, $imagePath, $announcementId);
    } else {
        $sql = "UPDATE 公告 SET 標題 = ?, 內容 = ? WHERE 公告ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $announcementId);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "公告已成功更新"]);
    } else {
        echo json_encode(["status" => "error", "message" => "公告更新失敗"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "無效的請求方法"]);
    http_response_code(405);
}
?>
