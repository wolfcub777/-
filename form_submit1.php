<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>停車資料輸入表單</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #1a73e8, #2a5298, #174ea6);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            color: #ffffff;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            font-size: 2rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            max-width: 400px;
            margin: 40px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
            color: #ffffff;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 1rem;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px rgba(118, 181, 255, 0.8);
        }

        button {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #ffffff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #0056b3, #003b8c);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }

        .history-button {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .history-button:hover {
            background: linear-gradient(135deg, #218838, #1c7430);
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 1.1rem;
            font-weight: bold;
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
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
                id_number VARCHAR(50) NOT NULL,
                parking_number VARCHAR(50) NOT NULL,
                license_plate VARCHAR(50) NOT NULL,
                exit_time DATETIME NOT NULL,
                marked TINYINT(1) DEFAULT 0,
                PRIMARY KEY (id_number, parking_number, license_plate)
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
        $id_number = $conn->real_escape_string($_POST['id_number']);
        $parking_number = $conn->real_escape_string($_POST['parking_number']);
        $license_plate = $conn->real_escape_string($_POST['license_plate']);
        $exit_time = $_POST['exit_time'];

        // 檢查是否有相同的資料存在
        $check_duplicate = "SELECT * FROM parking_entries 
                            WHERE id_number = '$id_number' 
                            AND parking_number = '$parking_number' 
                            AND license_plate = '$license_plate'";
        $result_duplicate = $conn->query($check_duplicate);

        if ($result_duplicate->num_rows > 0) {
            echo "<p style='text-align: center;'>該學號/教職員編號、停車格與車牌的記錄已存在，請勿重複提交。</p>";
        } else {
            // 判斷車牌號碼是否為認證車牌
            $marked = in_array($license_plate, $authorized_license_plates) ? 1 : 0;

            // 插入資料庫
            $sql_insert = "INSERT INTO parking_entries (id_number, parking_number, license_plate, exit_time, marked) 
                           VALUES ('$id_number', '$parking_number', '$license_plate', '$exit_time', $marked)";

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
        <label for="id_number">學號/教職員編號:</label>
        <input type="text" id="id_number" name="id_number" required><br><br>

        <label for="parking_number">停車格編號:</label>
        <input type="text" id="parking_number" name="parking_number" required><br><br>

        <label for="license_plate">車牌號碼:</label>
        <input type="text" id="license_plate" name="license_plate" required><br><br>

        <label for="exit_time">出場時間:</label>
        <input type="datetime-local" id="exit_time" name="exit_time" required><br><br>

        <button type="submit" name="submit">提交</button>
        <button type="button" class="back-button" onclick="window.location.href='學生.html'">返回主頁</button>
    </form>
</body>
</html>
