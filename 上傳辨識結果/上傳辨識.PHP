<?php
function 上傳多筆辨識結果() {
    // 設定檔案路徑（注意反斜線要跳脫）
    $filePath = "C:\\Users\\羅育政\\AppData\\Roaming\\0430.txt";

    // 檢查檔案是否存在
    if (!file_exists($filePath)) {
        echo "找不到檔案：$filePath";
        return;
    }

    // 讀取整份內容
    $content = file_get_contents($filePath);
    if (!$content) {
        echo "檔案為空或讀取失敗。";
        return;
    }

    // 使用正則表達式抓取所有車牌（格式為 AAA-1234）
    preg_match_all('/[A-Z]{3}-\d{4}/', strtoupper($content), $matches);
    $plates = $matches[0];

    if (empty($plates)) {
        echo "找不到任何車牌號碼。";
        return;
    }

    // 建立資料庫連線
    $host = "localhost";
    $dbname = "停車場巡查使用系統";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 準備 SQL
        $stmt = $pdo->prepare("INSERT INTO `辨識結果` (`辨識車牌`) VALUES (:plate)");

        $count = 0;
        foreach ($plates as $plate) {
            $stmt->bindParam(':plate', $plate);
            $stmt->execute();
            $count++;
        }

        echo "已成功寫入 $count 筆車牌資料。";

    } catch (PDOException $e) {
        echo "資料庫錯誤：" . $e->getMessage();
    }
}

// 呼叫副程式
上傳多筆辨識結果();
?>
