<?php
include 'config.php';

$sql = "SELECT id, sensor_id, condition_type, value, created_at, type, status FROM alertconditions";
$result = $conn->query($sql);

$conditions = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $conditions[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($conditions);

$conn->close();
?>
