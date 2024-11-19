<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車資料輸入表單</title>
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            position: relative;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }
        .header {
            background-color: #28a745;
            color: #fff;
            text-align: center;
            padding: 10px;
            border-radius: 12px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%; /* 確保標題與輸入表單寬度一致 */
        }
        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #e0e0e0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            margin-bottom: 15px;
            outline: none;
            transition: background 0.3s, transform 0.3s;
            box-shadow: inset 0 0 5px rgba(255, 255, 255, 0.3);
        }
        input:focus {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.03);
            box-shadow: 0 0 10px #00e6ff, 0 0 20px #00e6ff, 0 0 30px #00e6ff;
        }
        button {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
            box-shadow: 0 8px 15px rgba(255, 64, 108, 0.4);
            transition: all 0.3s ease-in-out;
        }
        button:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(255, 64, 108, 0.6);
        }
        .history-button {
            background: linear-gradient(135deg, #36d1dc, #5b86e5);
        }
        .history-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(91, 134, 229, 0.6);
        }
        p {
            text-align: center;
            color: #f3e8ff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">輸入停車資料</div>
        <?php
        // 資料庫連接與其他 PHP 程式碼保持不變
        ?>
        <form action="" method="POST">
            <label for="parking_number">停車格編號:</label>
            <input type="text" id="parking_number" name="parking_number" required>

            <label for="license_plate">車牌號碼:</label>
            <input type="text" id="license_plate" name="license_plate" required>

            <label for="entry_time">出場時間:</label>
            <input type="datetime-local" id="entry_time" name="entry_time" required>

            <button type="submit" name="submit">提交</button>
            <button type="button" class="history-button" onclick="window.location.href='view_history.php'">查看歷史紀錄</button>
        </form>
    </div>
</body>
</html>
