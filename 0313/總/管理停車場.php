<?php
include 'database.php';

// 取得所有停車場區域
$sql = "SELECT * FROM 停車場區域";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理停車場</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>停車場管理</h2>
<a href="新增停車場.html"><button>新增停車場</button></a>
<table>
    <tr>
        <th>名稱</th>
        <th>總車位</th>
        <th>圖片</th>
        <th>操作</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row["名稱"]; ?></td>
        <td><?php echo $row["總車位"]; ?></td>
        <td>
            <?php if ($row["圖片"]) { ?>
                <img src="<?php echo $row["圖片"]; ?>" alt="停車場圖片" width="100">
            <?php } else { echo "無圖片"; } ?>
        </td>
        <td>
            <a href="修改停車場.php?id=<?php echo $row['id']; ?>"><button>修改</button></a>
            <button onclick="deleteParking(<?php echo $row['id']; ?>)">刪除</button>
        </td>
    </tr>
    <?php } ?>
</table>

<script>
function deleteParking(id) {
    if (confirm("確定要刪除這個停車場嗎？")) {
        window.location.href = "刪除停車場.php?id=" + id;
    }
}
</script>

</body>
</html>

<?php $conn->close(); ?>
