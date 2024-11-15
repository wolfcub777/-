<?php
// 假設這些資料是從資料庫獲取
$user_data = [
    '帳號' => 'faculty',
    '職位' => '教師',
    '是否申請過停車資格' => '是'
];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教職員介面</title>
</head>
<body>
    <h2>歡迎, <?php echo $user_data['帳號']; ?></h2>
    <p>職位：<?php echo $user_data['職位']; ?></p>
    <p>是否申請過停車資格：<?php echo $user_data['是否申請過停車資格']; ?></p>
    <a href="apply_parking.php">申請停車資格</a>
</body>
</html>
