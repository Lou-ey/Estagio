<?php
include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, planta_path FROM plantas";
$result = $conn->query($sql);

$plantas = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $plantas[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($plantas);
?>

