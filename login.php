<?php
// 模擬的使用者資料，實際應從資料庫中獲取
$users = [
    'student' => ['username' => 'student', 'password' => 'password123', 'role' => 'A'],
    'guest' => ['username' => 'guest', 'password' => 'guestpass', 'role' => 'B'],
    'admin' => ['username' => 'admin', 'password' => 'adminpass', 'role' => 'C']
];

// 獲取表單提交的帳號與密碼
$username = $_POST['username'];
$password = $_POST['password'];

if (isset($users[$username]) && $users[$username]['password'] === $password) {
    // 根據角色轉導到不同的頁面
    switch ($users[$username]['role']) {
        case 'A':
            header('Location: student_dashboard.php');
            break;
        case 'B':
            header('Location: guest_dashboard.php');
            break;
        case 'C':
            header('Location: admin_dashboard.php');
            break;
    }
    exit();
} else {
    // 登入失敗提示
    echo "帳號或密碼錯誤";
}
?>
