<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_POST['planta_id'])) {
    $plantId = intval($_POST['planta_id']);
    $stmt = $conn->prepare("DELETE FROM plantas WHERE id = ?");
    $stmt->bind_param("i", $plantId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Planta excluída com sucesso.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir a planta: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID da planta não fornecido.']);
}

$conn->close();
?>
