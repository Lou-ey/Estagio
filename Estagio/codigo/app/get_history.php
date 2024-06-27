<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT id, timestamp, temperature, humidity, noise, air_quality FROM SensorData";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare falhou: " . $conn->error);
}

if ($stmt->execute() === false) {
    die("Execute falhou: " . $stmt->error);
}

$stmt->bind_result($id, $timestamp, $temperature, $humidity, $noise, $air_quality);

$data = [];

while ($stmt->fetch()) {
    $data[] = [
        'id' => $id,
        'timestamp' => $timestamp,
        'temperature' => $temperature,
        'humidity' => $humidity,
        'noise' => $noise,
        'air_quality' => $air_quality
    ];
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>

