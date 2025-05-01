<?php
include 'database.php';

if (!isset($_GET['id'])) {
    die("錯誤：未提供停車場 ID");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM 停車場區域 WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("錯誤：找不到該停車場");
}

$parking = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>修改停車場</title>
</head>
<body>
<h2>修改停車場</h2>
<form action="更新停車場.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $parking['id']; ?>">
    <label>名稱：</label>
    <input type="text" name="name" value="<?php echo $parking['名稱']; ?>" required><br>
    <label>總車位：</label>
    <input type="number" name="total_spots" value="<?php echo $parking['總車位']; ?>" required><br>
    <label>圖片：</label>
    <input type="file" name="image"><br>
    <img src="<?php echo $parking['圖片']; ?>" width="100"><br>
    <button type="submit">更新</button>
</form>
</body>
</html>
