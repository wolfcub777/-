<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

// 建立資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接是否成功
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "資料庫連接失敗: " . $conn->connect_error]));
}

// 接收 JSON 格式資料
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
    die(json_encode(["success" => false, "message" => "無效的資料格式"]));
}

// 準備 SQL 語句，避免覆蓋帳號欄位
$stmt = $conn->prepare(
    "INSERT INTO 使用者 (帳號, 名字, 身份, 聯絡資訊, 密碼, 車輛類型, 車牌號碼) 
     VALUES (?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE 
     名字 = VALUES(名字), 
     身份 = VALUES(身份), 
     聯絡資訊 = VALUES(聯絡資訊), 
     密碼 = VALUES(密碼), 
     車輛類型 = VALUES(車輛類型), 
     車牌號碼 = VALUES(車牌號碼)"
);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "SQL 準備失敗: " . $conn->error]));
}

// 回應結果陣列
$response = ["success" => true, "inserted" => [], "failed" => []];

// 執行插入
foreach ($data as $user) {
    // 驗證資料完整性
    if (
        empty($user['account']) || empty($user['name']) || empty($user['identity']) ||
        empty($user['contactInfo']) || empty($user['password']) || empty($user['vehicleType']) || empty($user['plateNumber'])
    ) {
        $response["failed"][] = [
            "account" => $user['account'] ?? null,
            "reason" => "資料不完整"
        ];
        continue;
    }

    // 綁定參數
    $stmt->bind_param(
        "sssssss",
        $user['account'],
        $user['name'],
        $user['identity'],
        $user['contactInfo'],
        $user['password'],
        $user['vehicleType'],
        $user['plateNumber']
    );

    // 執行插入並記錄結果
    if ($stmt->execute()) {
        $response["inserted"][] = $user['account'];
    } else {
        $response["failed"][] = [
            "account" => $user['account'],
            "reason" => $stmt->error
        ];
    }
}

$stmt->close();
$conn->close();

// 回傳結果
echo json_encode($response);
?>
