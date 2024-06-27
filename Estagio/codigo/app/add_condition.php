<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sensor']) && isset($_POST['type']) && isset($_POST['condition_type']) && isset($_POST['value'])) {
        $sensor = $_POST['sensor'];
        $type = $_POST['type'];
        $conditionType = $_POST['condition_type'];
        $value = $_POST['value'];

        // Verifica se já existe uma condição semelhante
        $sql_check = "SELECT id FROM AlertConditions WHERE sensor_id = ? AND type= ? AND condition_type = ? AND value = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("isss", $sensor, $type, $conditionType, $value);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            echo json_encode(array("success" => false, "message" => "Já existe uma condição semelhante para este sensor."));
        } else {
            // Insere a nova condição no banco de dados
            $sql_insert = "INSERT INTO AlertConditions (sensor_id, type, condition_type, value) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isss", $sensor, $type, $conditionType, $value);

            if ($stmt_insert->execute()) {
                echo json_encode(array("success" => true, "message" => "Condição adicionada com sucesso."));
            } else {
                echo json_encode(array("success" => false, "message" => "Erro ao adicionar a condição."));
            }
        }

        $stmt_check->close();
        $stmt_insert->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Campos incompletos."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Requisição inválida."));
}

$conn->close();
?>
