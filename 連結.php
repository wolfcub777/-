<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "停車場巡查使用系統";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連結失敗：" . $conn->connect_error);
}

$id = $_POST['id'];
$period = $_POST['period'];
$description = $_POST['description'];
$status = $description ? 'not-available' : 'available';

$sql = "UPDATE parking_spaces SET description = '$description', status = '$status', lastModified = NOW() WHERE id = $id AND period = '$period'";
$conn->query($sql);

$conn->close();
?>
