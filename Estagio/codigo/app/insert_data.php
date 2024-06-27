<?php
include 'config.php';

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive sensor credentials and data through a POST request
$sensor_username = $_POST['sensor_username'];
$sensor_password = $_POST['sensor_password'];
$temperature = $_POST['temperature'];
$humidity = $_POST['humidity'];
$noise = $_POST['noise'];
$air_quality = $_POST['air_quality'];

// Prepare the SQL statement to get sensor id based on username and password
$stmt = $conn->prepare("SELECT id FROM Sensors WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $sensor_username, $sensor_password);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    // Authentication successful
    $row = $result->fetch_assoc();
    $sensor_id = $row['id'];

    // Prepare and execute the SQL statement to insert sensor data
    $stmt = $conn->prepare("INSERT INTO SensorData (sensor_id, temperature, humidity, noise, air_quality) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idddd", $sensor_id, $temperature, $humidity, $noise, $air_quality);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // Authentication failed
    echo "Authentication failed";
}

$stmt->close();
$conn->close();
?>