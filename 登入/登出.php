<?php
session_start();
session_unset(); // 清除所有 session 變數
session_destroy(); // 結束 session
header("Location: 登入.html"); // 重新導向登入頁面
exit();
?>
