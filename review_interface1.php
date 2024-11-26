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

// 處理同意或拒絕請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $licensePlate = $_POST['licensePlate'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($licensePlate && $action) {
        // 查詢 "學生申請資料" 表中的資料
        $query = "SELECT * FROM 學生申請資料 WHERE 車牌號碼 = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $licensePlate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();

            // 插入到 "審核結果" 資料表
            $insertAuditQuery = "INSERT INTO 審核結果 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車輛類型, 車牌號碼, 審核結果)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $auditStmt = $conn->prepare($insertAuditQuery);
            $auditStmt->bind_param(
                "ssssssss",
                $userData['帳號'],
                $userData['名字'],
                $userData['身份'],
                $userData['聯絡資訊'],
                $userData['密碼'],
                $userData['車輛類型'],
                $userData['車牌號碼'],
                $action // "批准" 或 "拒絕"
            );
            $auditStmt->execute();

            if ($action === '批准') {
                // 插入到 "使用者" 資料表
                $insertUserQuery = "INSERT INTO 使用者 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車輛類型, 車牌號碼)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                $userStmt = $conn->prepare($insertUserQuery);
                $userStmt->bind_param(
                    "sssssss",
                    $userData['帳號'],
                    $userData['名字'],
                    $userData['身份'],
                    $userData['聯絡資訊'],
                    $userData['密碼'],
                    $userData['車輛類型'],
                    $userData['車牌號碼']
                );
                $userStmt->execute();
            }

            // 無論批准或拒絕，都從 "學生申請資料" 中刪除該行
            $deleteQuery = "DELETE FROM 學生申請資料 WHERE 車牌號碼 = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("s", $licensePlate);
            $deleteStmt->execute();
        }
    }
    exit; // 中止腳本執行，回傳空內容即可
}

// 從資料庫中獲取 "學生申請資料"
$sql = "SELECT * FROM 學生申請資料";
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
    </style>
</head>
<body>
    <button class="back-btn" onclick="goBack()">返回</button>
    <h1>審核申請</h1>
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
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row-<?php echo $row['車牌號碼']; ?>">
                        <td><?php echo htmlspecialchars($row['車牌號碼']); ?></td>
                        <td><?php echo htmlspecialchars($row['名字']); ?></td>
                        <td><?php echo htmlspecialchars($row['身份']); ?></td>
                        <td><?php echo htmlspecialchars($row['聯絡資訊']); ?></td>
                        <td><?php echo htmlspecialchars($row['密碼']); ?></td>
                        <td><?php echo htmlspecialchars($row['車輛類型']); ?></td>
                        <td><?php echo htmlspecialchars($row['帳號']); ?></td>
                        <td>
                            <button class="approve" onclick="handleAction('<?php echo $row['車牌號碼']; ?>', '批准')">批准</button>
                            <button class="reject" onclick="handleAction('<?php echo $row['車牌號碼']; ?>', '拒絕')">拒絕</button>
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
    <script>
        function handleAction(licensePlate, action) {
            fetch("", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    licensePlate: licensePlate,
                    action: action,
                }),
            })
                .then(response => {
                    if (response.ok) {
                        // 移除該行
                        document.getElementById("row-" + licensePlate).remove();
                        alert("車牌號碼 " + licensePlate + " 的操作 " + action + " 已完成");
                    } else {
                        alert("操作失敗，請稍後再試");
                    }
                })
                .catch(error => {
                    console.error("發生錯誤:", error);
                    alert("操作失敗，請檢查伺服器");
                });
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
