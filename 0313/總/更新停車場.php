<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $totalSpots = intval($_POST['total_spots']);

    // 處理圖片
    $imagePath = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

        // 更新資料庫（含圖片）
        $stmt = $conn->prepare("UPDATE 停車場區域 SET 名稱 = ?, 總車位 = ?, 圖片 = ? WHERE id = ?");
        $stmt->bind_param("sisi", $name, $totalSpots, $imagePath, $id);
    } else {
        // 更新資料庫（不更換圖片）
        $stmt = $conn->prepare("UPDATE 停車場區域 SET 名稱 = ?, 總車位 = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $totalSpots, $id);
    }

    if ($stmt->execute()) {
        echo "<script>
                alert('更新成功！');
                window.location.href = '管理停車場.php';
              </script>";
    } else {
        echo "<script>
                alert('更新失敗，請再試一次！');
                window.location.href = '修改停車場.php?id=$id';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
