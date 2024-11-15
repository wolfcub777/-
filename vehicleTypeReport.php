<?php
// 連接資料庫
include 'db_connection.php';

$sql = "SELECT 車輛類型, COUNT(*) as 數量 FROM 使用者 GROUP BY 車輛類型";
$result = $conn->query($sql);

echo "<h2>車輛類型統計報表</h2>";
echo "<table border='1'>
<tr>
<th>車輛類型</th>
<th>數量</th>
</tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['車輛類型'] . "</td>";
    echo "<td>" . $row['數量'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
