<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (is_array($data)) {
    foreach ($data as $polygon) {
        $plantId = $conn->real_escape_string($polygon['plantId']);
        $spaceId = $conn->real_escape_string($polygon['spaceId']);
        $spaceName = $conn->real_escape_string($polygon['spaceName']);
        $sensorIds = $conn->real_escape_string(json_encode($polygon['sensorIds']));
        $pointsJSON = $conn->real_escape_string(json_encode($polygon['points']));

        $checkSql = "SELECT * FROM polygons WHERE plant_id='$plantId' AND space_id='$spaceId' AND points='$pointsJSON'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            $sql = "UPDATE polygons SET space_name='$spaceName', sensor_ids='$sensorIds' WHERE plant_id='$plantId' AND space_id='$spaceId' AND points='$pointsJSON'";
        } else {
            $sql = "INSERT INTO polygons (plant_id, space_id, space_name, sensor_ids, points) VALUES ('$plantId', '$spaceId', '$spaceName', '$sensorIds', '$pointsJSON')";
        }

        if ($conn->query($sql) === TRUE) {
            echo "Polígono salvo com sucesso!<br>";
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    echo "Nenhum dado recebido ou formato de dados incorreto.";
}

$conn->close();
?>







