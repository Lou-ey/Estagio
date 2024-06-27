<?php
// Database connection configruration

$servername = "localhost";
$username = "Luis";
$password = "youshallnotpass";
$dbname = "digitalp_ComfortEvent";

/*
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ComfortEvent";*/

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the SQL statement to get the latest data from each sensor
$sql = "SELECT s.id, s.location, sd.temperature, sd.humidity, sd.noise, sd.air_quality, sd.timestamp
            FROM Sensors s
            INNER JOIN (
                SELECT sensor_id, MAX(timestamp) as MaxTime
                    FROM SensorData
                    GROUP BY sensor_id
            ) sdmax ON s.id = sdmax.sensor_id
            INNER JOIN SensorData sd ON s.id = sd.sensor_id AND sdmax.MaxTime = sd.timestamp";
$result = $conn->query($sql);
$data = array(); // Create a variable to hold the information
while ($row = $result->fetch_assoc()) {
    $data[] = $row; // add the row in to the results (data) variable
}
echo json_encode($data);

#echo json_encode($data); // echo the results back to the page

$conn->close();
?>