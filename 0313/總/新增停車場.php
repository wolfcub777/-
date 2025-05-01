<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $totalSpots = intval($_POST['total_spots']);

    $imagePath = NULL;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO 停車場區域 (名稱, 總車位, 圖片) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $totalSpots, $imagePath);

    if ($stmt->execute()) {
        echo "<script>
                alert('新增成功！');
                window.location.href = '管理停車場.php';
              </script>";
    } else {
        echo "<script>
                alert('新增失敗，請再試一次！');
                window.location.href = '新增停車場.html';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
