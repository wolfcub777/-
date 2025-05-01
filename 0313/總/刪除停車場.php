<?php
include 'database.php';

if (!isset($_GET['id'])) {
    die("錯誤：未提供停車場 ID");
}

$id = intval($_GET['id']);
$sql = "DELETE FROM 停車場區域 WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('刪除成功！');
            window.location.href = '管理停車場.php';
          </script>";
} else {
    echo "錯誤：" . $conn->error;
}

$conn->close();
?>
