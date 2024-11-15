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

// 從資料庫中獲取歷史資料
$sql = "SELECT * FROM 使用者";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>歷史紀錄</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
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
            text-align: left;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background-color: #218838;
        }
        .button-group {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>歷史資料記錄</h1>
    <div class="button-group">
        <button onclick="downloadCsv()">匯出CSV</button>
        <button onclick="goBack()">返回輸入頁面</button>
    </div>

    <form id="updateForm" method="POST" action="updateData.php">
        <table id="historyTable">
            <thead>
                <tr>
                    <th>帳號</th>
                    <th>名字</th>
                    <th>身份</th>
                    <th>聯絡資訊</th>
                    <th>密碼</th>
                    <th>車牌號碼</th>
                    <th>車輛類型</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['帳號']; ?><input type="hidden" name="account[]" value="<?php echo $row['帳號']; ?>"></td>
                    <td><span><?php echo $row['名字']; ?></span><input type="text" class="hidden" name="名字[]" value="<?php echo $row['名字']; ?>"></td>
                    <td>
                        <span><?php echo $row['身份']; ?></span>
                        <select class="hidden" name="身份[]">
                            <option value="學生" <?php if($row['身份'] == "學生") echo "selected"; ?>>學生</option>
                            <option value="教職員" <?php if($row['身份'] == "教職員") echo "selected"; ?>>教職員</option>
                        </select>
                    </td>
                    <td><span><?php echo $row['聯絡資訊']; ?></span><input type="text" class="hidden" name="聯絡資訊[]" value="<?php echo $row['聯絡資訊']; ?>"></td>
                    <td><span><?php echo $row['密碼']; ?></span><input type="text" class="hidden" name="密碼[]" value="<?php echo $row['密碼']; ?>"></td>
                    <td><span><?php echo $row['車牌號碼']; ?></span><input type="text" class="hidden" name="車牌號碼[]" value="<?php echo $row['車牌號碼']; ?>"></td>
                    <td>
                        <span><?php echo $row['車輛類型']; ?></span>
                        <select class="hidden" name="車輛類型[]">
                            <option value="機車" <?php if($row['車輛類型'] == "機車") echo "selected"; ?>>機車</option>
                            <option value="汽車" <?php if($row['車輛類型'] == "汽車") echo "selected"; ?>>汽車</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="editBtn">更改</button>
                        <button type="submit" class="hidden saveBtn">保存</button>
                        <button type="button" class="deleteBtn" data-account="<?php echo $row['帳號']; ?>">刪除</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>

    <script>
        function downloadCsv() {
            const rows = document.querySelectorAll('#historyTable tr');
            let csvContent = "﻿帳號,名字,身份,聯絡資訊,密碼,車牌號碼,車輛類型\n";  // 增加 UTF-8 BOM

            rows.forEach(row => {
                const columns = row.querySelectorAll('td');
                let rowData = [];
                columns.forEach((column, index) => {
                    if (index < columns.length - 1) {
                        let cellContent = column.querySelector('input, select') ? column.querySelector('input, select').value : column.innerText.trim();
                        rowData.push(cellContent);
                    }
                });
                csvContent += rowData.join(",") + "\n";
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "history_data.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function goBack() {
            window.location.href = '輸入.html';  // 返回輸入頁面
        }

        document.querySelectorAll('.editBtn').forEach(function(editButton) {
            editButton.addEventListener('click', function() {
                const row = this.parentElement.parentElement;
                const cells = row.querySelectorAll('td');
                const saveButton = this.nextElementSibling;

                cells.forEach(cell => {
                    let span = cell.querySelector('span');
                    let input = cell.querySelector('input, select');
                    if (span && input) {
                        span.classList.add('hidden');
                        input.classList.remove('hidden');
                    }
                });

                this.classList.add('hidden');
                saveButton.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function() {
                const account = this.getAttribute('data-account');
                const confirmed = confirm("你確定要刪除這筆資料嗎？");

                if (confirmed) {
                    window.location.href = 'deleteData.php?account=' + account;
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
