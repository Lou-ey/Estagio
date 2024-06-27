<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recebe os dados do formulário
if (isset($_POST['sensor_id'])) {
    $sensor_id = $_POST['sensor_id'];

    // Verifica se o sensor está associado a algum espaço
    $sql_check_association = "SELECT espaco_id FROM espacos_sensores WHERE sensor_id = ?";
    $stmt_check_association = $conn->prepare($sql_check_association);
    $stmt_check_association->bind_param("i", $sensor_id);
    $stmt_check_association->execute();
    $stmt_check_association->store_result();

    if ($stmt_check_association->num_rows > 0) {
        // Remove a associação do sensor com o espaço na tabela espacos_sensores
        $sql_remove_association = "DELETE FROM espacos_sensores WHERE sensor_id = ?";
        $stmt_remove_association = $conn->prepare($sql_remove_association);
        $stmt_remove_association->bind_param("i", $sensor_id);
        $stmt_remove_association->execute();
        $stmt_remove_association->close();
    }

    // Remove o sensor da tabela sensors
    $sql_remove_sensor = "DELETE FROM sensors WHERE id = ?";
    $stmt_remove_sensor = $conn->prepare($sql_remove_sensor);
    $stmt_remove_sensor->bind_param("i", $sensor_id);
    if ($stmt_remove_sensor->execute()) {
        $response['success'] = true;
        $response['message'] = 'Sensor removido com sucesso.';
    } else {
        $response['success'] = false;
        $response['message'] = 'Erro ao remover sensor: ' . $stmt_remove_sensor->error;
    }

    echo json_encode($response);

    $stmt_remove_sensor->close();
} else {
    $response['success'] = false;
    $response['message'] = 'ID do sensor não fornecido.';
    echo json_encode($response);
}

$conn->close();
?>

