<?php
session_start();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>巡查人員首頁</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: 50px;
        }
        .menu {
            margin-top: 50px;
        }
        .menu button {
            padding: 15px 30px;
            font-size: 18px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            transition: 0.3s;
        }
        .menu button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h2>歡迎巡查人員</h2>
    <div class="menu">
        <button onclick="window.location.href='登出.php'">登出</button>
    </div>
</body>
</html>
