<?php
// 連接資料庫
include 'db_connection.php';

$sql = "SELECT DATE(停車日期時間) as 日期, COUNT(*) as 停車次數 FROM 停車記錄 GROUP BY DATE(停車日期時間)";
$result = $conn->query($sql);

echo "<h2>每日停車次數統計報表</h2>";
echo "<table border='1'>
<tr>
<th>日期</th>
<th>停車次數</th>
</tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['日期'] . "</td>";
    echo "<td>" . $row['停車次數'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
