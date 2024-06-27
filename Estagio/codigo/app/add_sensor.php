<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (
    isset($_POST['location']) &&
    isset($_POST['status']) &&
    isset($_POST['username']) &&
    isset($_POST['password'])
) {
    $espaco_id = $_POST['location'];

    $sql_espaco = "SELECT nome FROM espacos WHERE id = ?";
    $stmt_espaco = $conn->prepare($sql_espaco);
    $stmt_espaco->bind_param("i", $espaco_id);
    $stmt_espaco->execute();
    $stmt_espaco->store_result();

    $location = null;

    if ($stmt_espaco->num_rows > 0) {
        $stmt_espaco->bind_result($location);
        $stmt_espaco->fetch();
    }

    $stmt_espaco->close();

    if ($location !== null) {
        $status = $_POST['status'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "INSERT INTO sensors (location, status, username, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $location, $status, $username, $password);

        $response = array();

        if ($stmt->execute()) {
            $sensor_id = $stmt->insert_id;

            $sql_espacos_sensores = "INSERT INTO espacos_sensores (espaco_id, sensor_id) VALUES (?, ?)";
            $stmt_espacos_sensores = $conn->prepare($sql_espacos_sensores);
            $stmt_espacos_sensores->bind_param("ii", $espaco_id, $sensor_id);

            if ($stmt_espacos_sensores->execute()) {
                $response['success'] = true;
                $response['message'] = 'Sensor adicionado com sucesso.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Erro ao adicionar sensor: ' . $stmt_espacos_sensores->error;
            }

            $stmt_espacos_sensores->close();
        } else {
            $response['success'] = false;
            $response['message'] = 'Erro ao adicionar sensor: ' . $stmt->error;
        }

        echo json_encode($response);

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = 'Espaço não encontrado.';
        echo json_encode($response);
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Todos os campos são obrigatórios.';
    echo json_encode($response);
}

$conn->close();
?>



