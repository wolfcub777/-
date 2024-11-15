<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車資料輸入表單</title>
    <style>
        body {
            background-image: url('/55.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            color: #333;
        }
        form {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            margin: 100px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .history-button {
            background-color: #007BFF;
        }
        .history-button:hover {
            background-color: #0056b3;
        }
        .back-button {
            background-color: #6c757d;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h2>輸入停車資料</h2>

    <?php
    // 資料庫連接設定
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "停車場巡查使用系統";

    // 建立資料庫連接
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("連接失敗: " . $conn->connect_error);
    }

    // 檢查是否存在 parking_entries 表格，若不存在則建立該表格
    $sql_check_table = "SHOW TABLES LIKE 'parking_entries'";
    $result_table = $conn->query($sql_check_table);

    if ($result_table->num_rows == 0) {
        $sql_create_table = "
            CREATE TABLE parking_entries (
                parking_number VARCHAR(50) NOT NULL,
                license_plate VARCHAR(50) NOT NULL,
                exit_time DATETIME NOT NULL,
                marked TINYINT(1) DEFAULT 0,
                PRIMARY KEY (parking_number, license_plate)
            )";
        if ($conn->query($sql_create_table) === TRUE) {
            echo "<p style='text-align: center;'>已建立資料表 parking_entries。</p>";
        } else {
            die("<p style='text-align: center;'>建立資料表失敗: " . $conn->error . "</p>");
        }
    }

    // 認證的車牌號碼
    $authorized_license_plates = ['MXH-1122', 'NXD-3988', 'PUBG-2486'];

    // 表單提交處理
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $parking_number = $conn->real_escape_string($_POST['parking_number']);
        $license_plate = $conn->real_escape_string($_POST['license_plate']);
        $exit_time = $_POST['exit_time'];

        // 檢查是否有相同的資料存在
        $check_duplicate = "SELECT * FROM parking_entries 
                            WHERE parking_number = '$parking_number' 
                            AND license_plate = '$license_plate'";
        $result_duplicate = $conn->query($check_duplicate);

        if ($result_duplicate->num_rows > 0) {
            echo "<p style='text-align: center;'>該停車格與車牌的記錄已存在，請勿重複提交。</p>";
        } else {
            // 判斷車牌號碼是否為認證車牌
            $marked = in_array($license_plate, $authorized_license_plates) ? 1 : 0;

            // 插入資料庫
            $sql_insert = "INSERT INTO parking_entries (parking_number, license_plate, exit_time, marked) 
                           VALUES ('$parking_number', '$license_plate', '$exit_time', $marked)";

            if ($conn->query($sql_insert) === TRUE) {
                echo "<p style='text-align: center;'>資料已提交。" . ($marked ? " 並已標記為認證車牌。" : " 未標記。") . "</p>";
            } else {
                echo "<p style='text-align: center;'>提交失敗: " . $conn->error . "</p>";
            }
        }
    }

    $conn->close();
    ?>

    <form action="" method="POST">
        <label for="parking_number">停車格編號:</label>
        <input type="text" id="parking_number" name="parking_number" required><br><br>

        <label for="license_plate">車牌號碼:</label>
        <input type="text" id="license_plate" name="license_plate" required><br><br>

        <label for="exit_time">出場時間:</label>
        <input type="datetime-local" id="exit_time" name="exit_time" required><br><br>

        <button type="submit" name="submit">提交</button>
        <button type="button" class="history-button" onclick="window.location.href='view_history.php'">查看歷史紀錄</button>
        <button type="button" class="back-button" onclick="window.location.href='合體巨獸.html'">返回主頁</button>
    </form>
</body>
</html>
