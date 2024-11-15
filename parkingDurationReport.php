<?php
// 連接資料庫
include 'db_connection.php';

$sql = "SELECT 車牌號碼, TIMESTAMPDIFF(HOUR, 停車日期時間, 停車結束時間) as 停留時長 FROM 停車記錄";
$result = $conn->query($sql);

echo "<h2>車輛停留時長報表</h2>";
echo "<table border='1'>
<tr>
<th>車牌號碼</th>
<th>停留時長(小時)</th>
</tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['車牌號碼'] . "</td>";
    echo "<td>" . $row['停留時長'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
