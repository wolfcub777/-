<?php
include 'db_connection.php'; // 連接資料庫

// 從表單接收資料
$account = $_POST['account'];
$name = $_POST['name'];
$password = $_POST['password'];
$contactInfo = $_POST['contactInfo'];
$vehicleType = $_POST['vehicleType'];
$plateNumber = $_POST['plateNumber'];
$identity = '學生'; // 身份默認為學生

// 檢查使用者資料表中是否已有相同的帳號
$check_sql = "SELECT * FROM 使用者 WHERE 帳號 = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $account);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // 若已存在相同的帳號，顯示錯誤訊息並返回
    echo "<script>alert('此帳號已申請過，無法再度申請。'); window.history.back();</script>";
    $check_stmt->close();
    $conn->close();
    exit;
}

$check_stmt->close();

// 檢查學生申請資料表中是否已有相同的帳號
$check_sql_student = "SELECT * FROM 學生申請資料 WHERE 帳號 = ?";
$check_stmt_student = $conn->prepare($check_sql_student);
$check_stmt_student->bind_param("s", $account);
$check_stmt_student->execute();
$result_student = $check_stmt_student->get_result();

if ($result_student->num_rows > 0) {
    // 若已存在相同的帳號，顯示錯誤訊息並返回
    echo "<script>alert('此帳號正在待審核中，無法再度申請。'); window.history.back();</script>";
    $check_stmt_student->close();
    $conn->close();
    exit;
}

$check_stmt_student->close();

// 若帳號不存在於使用者和學生申請資料表中，則插入資料到待審核資料表
$sql = "INSERT INTO 學生申請資料 (帳號, 名字, 身份, 密碼, 聯絡資訊, 車輛類型, 車牌號碼) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "<script>alert('SQL語句準備失敗：" . htmlspecialchars($conn->error) . "'); window.history.back();</script>";
    $conn->close();
    exit;
}

$stmt->bind_param(
    "sssssss", 
    $account, $name, $identity, $password, $contactInfo, $vehicleType, $plateNumber
);

if ($stmt->execute()) {
    echo "<script>alert('申請已提交成功，等待總務處審核。'); window.location.href='學生停車資格申請.html';</script>";
} else {
    echo "<script>alert('提交失敗：" . htmlspecialchars($stmt->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
