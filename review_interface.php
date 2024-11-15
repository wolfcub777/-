<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 自動建立 "審核結果" 資料表 (如果不存在)
$table_sql = "CREATE TABLE IF NOT EXISTS 審核結果 (
    帳號 VARCHAR(50) PRIMARY KEY,
    名字 VARCHAR(100) NOT NULL,
    身份 VARCHAR(50) NOT NULL,
    聯絡資訊 VARCHAR(100),
    密碼 VARCHAR(100) NOT NULL,
    車輛類型 ENUM('汽車', '機車') NOT NULL,
    車牌號碼 VARCHAR(20) NOT NULL,
    審核結果 ENUM('批准', '拒絕') NOT NULL
)";

if ($conn->query($table_sql) === FALSE) {
    die("資料表建立失敗: " . $conn->error);
}

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['temporaryList'])) {
    $temporaryList = json_decode($_POST['temporaryList'], true);

    foreach ($temporaryList as $item) {
        if (isset($item['licensePlate'])) {
            $licensePlate = $item['licensePlate'];
            $action = $item['action'];

            // 從 "使用者" 表中查詢車牌號碼詳細資訊
            $query = "SELECT * FROM 使用者 WHERE 車牌號碼 = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $licensePlate);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $userData = $result->fetch_assoc();

                // 插入到 "審核結果" 資料表
                $insertQuery = "INSERT INTO 審核結果 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車輛類型, 車牌號碼, 審核結果)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE 審核結果 = ?";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param(
                    "sssssssss",
                    $userData['帳號'],
                    $userData['名字'],
                    $userData['身份'],
                    $userData['聯絡資訊'],
                    $userData['密碼'],
                    $userData['車輛類型'],
                    $userData['車牌號碼'],
                    $action,
                    $action
                );
                $insertStmt->execute();

                // 從 "使用者" 表中刪除該車牌號碼的資料
                $deleteQuery = "DELETE FROM 使用者 WHERE 車牌號碼 = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("s", $licensePlate);
                $deleteStmt->execute();
            }
        } else {
            echo "<script>console.error('Error: licensePlate key is not defined in one of the items.');</script>";
        }
    }
    // 顯示提交成功訊息並清除暫存資料
    echo "<script>alert('資料已成功送出。'); localStorage.removeItem('temporaryList');</script>";
}

// 從資料庫中獲取未審核的歷史紀錄
$sql = "SELECT * FROM 使用者";
$result = $conn->query($sql);

if (!$result) {
    die("查詢失敗: " . $conn->error . " (錯誤代碼: " . $conn->errno . ")");
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>審核申請介面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        button {
            padding: 8px 16px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .approve {
            background-color: #4CAF50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
        .submit-all {
            background-color: #FF9800;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .view-temp-list {
            background-color: #03A9F4;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .back-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .back-btn:hover {
            background-color: #1976D2;
        }
        #tempListModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 2px solid #000;
            padding: 20px;
            z-index: 1000;
            width: 80%;
        }
        #tempListModal table {
            width: 100%;
        }
        #modalClose {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body onload="loadTemporaryList()">
    <button class="back-btn" onclick="goBack()">返回</button>

    <h1>審核申請</h1>
    <form id="reviewForm" method="POST" action="">
        <table>
            <thead>
                <tr>
                    <th>車牌號碼</th>
                    <th>名字</th>
                    <th>身份</th>
                    <th>聯絡資訊</th>
                    <th>密碼</th>
                    <th>車輛類型</th>
                    <th>帳號</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr id="row-<?php echo $row['車牌號碼']; ?>">
                            <td><?php echo htmlspecialchars($row['車牌號碼']); ?></td>
                            <td><?php echo htmlspecialchars($row['名字']); ?></td>
                            <td><?php echo htmlspecialchars($row['身份']); ?></td>
                            <td><?php echo htmlspecialchars($row['聯絡資訊']); ?></td>
                            <td><?php echo htmlspecialchars($row['密碼']); ?></td>
                            <td><?php echo htmlspecialchars($row['車輛類型']); ?></td>
                            <td><?php echo htmlspecialchars($row['帳號']); ?></td>
                            <td>
                                <button class="approve" type="button" onclick="addToTemporaryList('<?php echo $row['車牌號碼']; ?>', '批准')">批准</button>
                                <button class="reject" type="button" onclick="addToTemporaryList('<?php echo $row['車牌號碼']; ?>', '拒絕')">拒絕</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">無申請資料</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <input type="hidden" id="temporaryList" name="temporaryList" value="">
        <button type="submit" class="submit-all">一次送出</button>
        <button type="button" class="view-temp-list" onclick="showTemporaryList()">查看暫存列表</button>
    </form>

    <!-- Modal for viewing temporary list -->
    <div id="tempListModal">
        <h2>暫存列表</h2>
        <table>
            <thead>
                <tr>
                    <th>車牌號碼</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="tempListTableBody"></tbody>
        </table>
        <button id="modalClose" onclick="closeModal()">關閉</button>
    </div>

    <script>
        let tempList = [];

        function addToTemporaryList(licensePlate, action) {
            const existingIndex = tempList.findIndex(item => item.licensePlate === licensePlate);
            if (existingIndex !== -1) {
                tempList[existingIndex].action = action; // 更新操作類型
            } else {
                tempList.push({ licensePlate: licensePlate, action: action });
            }
            document.getElementById('temporaryList').value = JSON.stringify(tempList);
            saveTemporaryList();
            alert('車牌號碼 ' + licensePlate + ' 已加入暫存列表，操作: ' + action);

            // 移除該行
            const row = document.getElementById('row-' + licensePlate);
            if (row) {
                row.remove();
            }
        }

        function showTemporaryList() {
            if (tempList.length === 0) {
                alert("暫存列表為空");
                return;
            }

            const tableBody = document.getElementById('tempListTableBody');
            tableBody.innerHTML = ""; // 清空之前的內容

            tempList.forEach(item => {
                const row = document.createElement('tr');
                const licensePlateCell = document.createElement('td');
                licensePlateCell.textContent = item.licensePlate;
                const actionCell = document.createElement('td');
                actionCell.textContent = item.action;
                row.appendChild(licensePlateCell);
                row.appendChild(actionCell);
                tableBody.appendChild(row);
            });

            document.getElementById('tempListModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('tempListModal').style.display = 'none';
        }

        function saveTemporaryList() {
            localStorage.setItem('temporaryList', JSON.stringify(tempList));
        }

        function loadTemporaryList() {
            const savedList = localStorage.getItem('temporaryList');
            if (savedList) {
                tempList = JSON.parse(savedList);
                document.getElementById('temporaryList').value = JSON.stringify(tempList);
            }
        }

        function goBack() {
            window.history.back(); // 返回上一頁
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
