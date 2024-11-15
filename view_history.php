<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看歷史紀錄</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .back-button, .filter-button {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover, .filter-button:hover {
            background-color: #0056b3;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        select, input[type="text"] {
            padding: 5px;
            margin-right: 10px;
        }
        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">停車歷史紀錄</h2>

    <!-- 篩選表單 -->
    <form method="GET" action="">
        <label for="filter_license_plate">篩選車牌號碼:</label>
        <input type="text" id="filter_license_plate" name="filter_license_plate" placeholder="輸入車牌號碼">
        
        <label for="filter_marked">篩選是否認證:</label>
        <select id="filter_marked" name="filter_marked">
            <option value="">全部</option>
            <option value="1">是</option>
            <option value="0">否</option>
        </select>
        
        <button type="submit">篩選</button>
    </form>

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

    // 構建篩選條件
    $filter_license_plate = isset($_GET['filter_license_plate']) ? $conn->real_escape_string($_GET['filter_license_plate']) : '';
    $filter_marked = isset($_GET['filter_marked']) ? $conn->real_escape_string($_GET['filter_marked']) : '';

    $sql = "SELECT parking_number, license_plate, exit_time, marked FROM parking_entries WHERE 1=1";

    if ($filter_license_plate !== '') {
        $sql .= " AND license_plate LIKE '%$filter_license_plate%'";
    }
    if ($filter_marked !== '') {
        $sql .= " AND marked = '$filter_marked'";
    }

    $sql .= " ORDER BY exit_time DESC";

    $result = $conn->query($sql);

    if ($result === false) {
        echo "<p style='text-align: center;'>查詢失敗: " . $conn->error . "</p>";
    } elseif ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>停車格編號</th><th>車牌號碼</th><th>出場時間</th><th>是否認證</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['parking_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['license_plate']) . "</td>";
            echo "<td>" . htmlspecialchars($row['exit_time']) . "</td>";
            echo "<td>" . ($row['marked'] ? "是" : "否") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align: center;'>目前無歷史紀錄。</p>";
    }

    $conn->close();
    ?>

    <a href="form_submit.php" class="back-button">返回輸入頁面</a>
</body>
</html>
