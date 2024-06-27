<?php
include 'config.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o ID e o plantId foram enviados via POST
if (isset($_POST['id']) && isset($_POST['plantId'])) {
    $polygonId = intval($_POST['id']);
    $plantId = intval($_POST['plantId']);

    // Prepara a declaração SQL para excluir o polígono
    $stmt = $conn->prepare("DELETE FROM polygons WHERE id = ? AND plant_id = ?");
    $stmt->bind_param("ii", $polygonId, $plantId);

    if ($stmt->execute()) {
        echo "Polígono excluído com sucesso.";
    } else {
        echo "Erro ao excluir o polígono: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID do polígono ou plantId não fornecido.";
}

$conn->close();
?>
