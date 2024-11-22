<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 查詢最新公告
$sqlLatest = "SELECT * FROM 公告 WHERE 是否最新 = 1";
$resultLatest = $conn->query($sqlLatest);

// 查詢歷史公告
$sqlHistory = "SELECT * FROM 公告 WHERE 是否最新 = 0 ORDER BY 發布時間 DESC";
$resultHistory = $conn->query($sqlHistory);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看已發布公告</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            background-size: 400% 400%;
            animation: gradientAnimation 10s ease infinite;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 4px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
            margin-bottom: 20px;
        }

        .announcement {
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .announcement:hover {
            transform: scale(1.02);
        }

        .announcement h2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .announcement-details {
            display: none;
            margin-top: 10px;
        }

        .edit-form {
            margin-top: 10px;
            display: none;
            flex-direction: column;
        }

        .save-button, .edit-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .save-button {
            background-color: #28a745;
            color: white;
        }

        .save-button:hover {
            background-color: #218838;
        }

        .edit-button {
            background-color: #ffc107;
            color: white;
        }

        .edit-button:hover {
            background-color: #e0a800;
        }

        textarea, input[type="text"], input[type="file"] {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            const arrow = document.getElementById(`arrow-${id}`);
            if (details.style.display === 'none') {
                details.style.display = 'block';
                arrow.textContent = '▲';
            } else {
                details.style.display = 'none';
                arrow.textContent = '▼';
            }
        }

        function toggleEditForm(id) {
            const form = document.getElementById(`edit-form-${id}`);
            if (form.style.display === 'none') {
                form.style.display = 'flex';
            } else {
                form.style.display = 'none';
            }
        }

        function saveEdit(id) {
            const title = document.getElementById(`edit-title-${id}`).value.trim();
            const content = document.getElementById(`edit-content-${id}`).value.trim();

            if (!title || !content) {
                alert('標題或內容不能為空');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_announcement.php', true);

            const formData = new FormData();
            formData.append('id', id);
            formData.append('title', title);
            formData.append('content', content);

            const imageInput = document.getElementById(`edit-image-${id}`);
            if (imageInput && imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        document.getElementById(`title-${id}`).textContent = title;
                        document.getElementById(`content-${id}`).innerHTML = content.replace(/\n/g, '<br>');
                        if (imageInput && imageInput.files[0]) {
                            const imagePreview = document.getElementById(`image-${id}`);
                            imagePreview.src = URL.createObjectURL(imageInput.files[0]);
                        }
                        alert('公告已成功更新');
                        toggleEditForm(id);
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send(formData);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>最新公告</h1>
        <?php
        if ($resultLatest->num_rows > 0) {
            while ($row = $resultLatest->fetch_assoc()) {
                echo "<div class='announcement'>";
                echo "<h2 onclick='toggleDetails({$row['公告ID']})' id='title-{$row['公告ID']}'>" . htmlspecialchars($row['標題']) . "<span id='arrow-{$row['公告ID']}' class='arrow'>▼</span></h2>";
                echo "<div id='details-{$row['公告ID']}' class='announcement-details'>";
                echo "<p class='timestamp'>" . $row['發布時間'] . "</p>";
                echo "<p id='content-{$row['公告ID']}'>" . nl2br(htmlspecialchars($row['內容'])) . "</p>";
                if (!empty($row['image_path'])) {
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='公告圖片' id='image-{$row['公告ID']}'>";
                }
                echo "<div id='edit-form-{$row['公告ID']}' class='edit-form'>";
                echo "<input type='text' id='edit-title-{$row['公告ID']}' value='" . htmlspecialchars($row['標題']) . "'>";
                echo "<textarea id='edit-content-{$row['公告ID']}' rows='4'>" . htmlspecialchars($row['內容']) . "</textarea>";
                echo "<input type='file' id='edit-image-{$row['公告ID']}' accept='image/*'>";
                echo "<button class='save-button' onclick='saveEdit({$row['公告ID']})'>保存</button>";
                echo "</div>";
                echo "<button class='edit-button' onclick='toggleEditForm({$row['公告ID']})'>編輯</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>目前沒有最新公告。</p>";
        }
        ?>
    </div>

    <div class="container">
        <h1>歷史公告</h1>
        <?php
        if ($resultHistory->num_rows > 0) {
            while ($row = $resultHistory->fetch_assoc()) {
                echo "<div class='announcement'>";
                echo "<h2 onclick='toggleDetails({$row['公告ID']})' id='title-{$row['公告ID']}'>" . htmlspecialchars($row['標題']) . "<span id='arrow-{$row['公告ID']}' class='arrow'>▼</span></h2>";
                echo "<div id='details-{$row['公告ID']}' class='announcement-details'>";
                echo "<p class='timestamp'>" . $row['發布時間'] . "</p>";
                echo "<p id='content-{$row['公告ID']}'>" . nl2br(htmlspecialchars($row['內容'])) . "</p>";
                if (!empty($row['image_path'])) {
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='公告圖片' id='image-{$row['公告ID']}'>";
                }
                echo "<div id='edit-form-{$row['公告ID']}' class='edit-form'>";
                echo "<input type='text' id='edit-title-{$row['公告ID']}' value='" . htmlspecialchars($row['標題']) . "'>";
                echo "<textarea id='edit-content-{$row['公告ID']}' rows='4'>" . htmlspecialchars($row['內容']) . "</textarea>";
                echo "<input type='file' id='edit-image-{$row['公告ID']}' accept='image/*'>";
                echo "<button class='save-button' onclick='saveEdit({$row['公告ID']})'>保存</button>";
                echo "</div>";
                echo "<button class='edit-button' onclick='toggleEditForm({$row['公告ID']})'>編輯</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>目前沒有歷史公告。</p>";
        }
        $conn->close();
        ?>
    </div>
    <a href="公告.html" class="back-button">返回</a>
</body>
</html>
