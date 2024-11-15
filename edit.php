<?php
// 連接資料庫
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

// 獲取表單提交的使用者ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 從資料庫獲取使用者資料
    $sql = "SELECT * FROM 使用者 WHERE 使用者ID = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 獲取使用者資料
        $row = $result->fetch_assoc();
        $name = $row['名字'];
        $identity = $row['身份'];
        $contactInfo = $row['聯絡資訊'];
        $account = $row['帳號'];
        $password = $row['密碼'];
        $plateNumber = $row['車牌號碼'];
        $vehicleType = $row['車輛類型'];
    } else {
        echo "找不到使用者";
        exit();
    }
}

// 保存更改資料
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $identity = $_POST['identity'];
    $contactInfo = $_POST['contactInfo'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $plateNumber = $_POST['plateNumber'];
    $vehicleType = $_POST['vehicleType'];

    // 更新資料庫
    $sql = "UPDATE 使用者 SET 名字='$name', 身份='$identity', 聯絡資訊='$contactInfo', 帳號='$account', 密碼='$password', 車牌號碼='$plateNumber', 車輛類型='$vehicleType' WHERE 使用者ID=$id";

    if ($conn->query($sql) === TRUE) {
        echo "更新成功";
        header("Location: history.php");
        exit();
    } else {
        echo "更新失敗: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更改使用者資料</title>
</head>
<body>
    <h1>更改使用者資料</h1>
    <form method="post" action="edit.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="name">名字:</label>
        <input type="text" name="name" value="<?php echo $name; ?>" required><br>

        <label for="identity">身份:</label>
        <input type="text" name="identity" value="<?php echo $identity; ?>" required><br>

        <label for="contactInfo">聯絡資訊:</label>
        <input type="text" name="contactInfo" value="<?php echo $contactInfo; ?>" required><br>

        <label for="account">帳號:</label>
        <input type="text" name="account" value="<?php echo $account; ?>" required><br>

        <label for="password">密碼:</label>
        <input type="text" name="password" value="<?php echo $password; ?>" required><br>

        <label for="plateNumber">車牌號碼:</label>
        <input type="text" name="plateNumber" value="<?php echo $plateNumber; ?>" required><br>

        <label for="vehicleType">車輛類型:</label>
        <select name="vehicleType">
            <option value="汽車" <?php if ($vehicleType == '汽車') echo 'selected'; ?>>汽車</option>
            <option value="機車" <?php if ($vehicleType == '機車') echo 'selected'; ?>>機車</option>
        </select><br>

        <button type="submit" name="update">保存更改</button>
    </form>
</body>
</html>
