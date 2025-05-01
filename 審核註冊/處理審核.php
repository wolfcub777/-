<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $user_ids = json_decode($_POST["user_ids"], true); // 解析 JSON 陣列

    if (!$user_ids || count($user_ids) == 0) {
        die("❌ 錯誤：沒有選擇任何用戶！");
    }

    foreach ($user_ids as $user_id) {
        // 查詢該使用者的資料
        $sql = "SELECT * FROM `帳戶註冊` WHERE 學號 = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("❌ SQL 準備失敗：" . $conn->error);
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            echo "❌ 找不到學號：$user_id";
            continue;
        }

        if ($action === "approve") {
            // 檢查 `新版使用者` 是否已經有此學號
            $check_sql = "SELECT 學號 FROM `新版使用者` WHERE 學號 = ?";
            $check_stmt = $conn->prepare($check_sql);
            if (!$check_stmt) {
                die("❌ SQL 檢查錯誤：" . $conn->error);
            }
            $check_stmt->bind_param("s", $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo "⚠️ 該學號 $user_id 已註冊過，跳過新增";
                continue;
            }

            // 插入資料到 `新版使用者`
            $insert_sql = "INSERT INTO `新版使用者` (學號, 身分, 學部, 科別, 學制, 姓名, 電話, 信箱, 身分證, 車牌號碼, 圖片)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insert_stmt = $conn->prepare($insert_sql);
            if (!$insert_stmt) {
                die("❌ SQL 插入錯誤：" . $conn->error);
            }
            $insert_stmt->bind_param(
                "sssssssssss",
                $user['學號'],
                $user['身分'],
                $user['學部'],
                $user['科別'],
                $user['學制'],
                $user['姓名'],
                $user['電話'],
                $user['信箱'],
                $user['身分證'],
                $user['車牌號碼'],
                $user['圖片']
            );

            if ($insert_stmt->execute()) {
                echo "✅ 學號 $user_id 已同意註冊！";
            } else {
                echo "❌ 插入失敗：" . $insert_stmt->error . "<br>";
            }

            // 刪除 `帳戶註冊` 中已審核的資料
            $delete_sql = "DELETE FROM `帳戶註冊` WHERE 學號 = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            if (!$delete_stmt) {
                die("❌ SQL 刪除錯誤：" . $conn->error);
            }
            $delete_stmt->bind_param("s", $user_id);
            $delete_stmt->execute();
        } elseif ($action === "reject") {
            // 直接刪除 `帳戶註冊` 資料
            $delete_sql = "DELETE FROM `帳戶註冊` WHERE 學號 = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            if (!$delete_stmt) {
                die("❌ SQL 刪除錯誤：" . $conn->error);
            }
            $delete_stmt->bind_param("s", $user_id);
            $delete_stmt->execute();
            echo "❌ 學號 $user_id 申請已被拒絕";
        }
    }
}

$conn->close();
?>
