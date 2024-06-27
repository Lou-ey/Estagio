<?php
include 'config.php';

header('Content-Type: application/json');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, plant_id, space_id, space_name, sensor_ids, points FROM polygons";
$result = $conn->query($sql);

$polygons = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $polygon = [
            'id' => $row['id'],
            'plantId' => $row['plant_id'],
            'space_id' => $row['space_id'],
            'space_name' => $row['space_name'],
            'sensor_ids' => explode(',', $row['sensor_ids']),
            'points' => json_decode($row['points'], true)
        ];
        array_push($polygons, $polygon);
    }
}

$conn->close();

echo json_encode(['polygons' => $polygons]);
?>


