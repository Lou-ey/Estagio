<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['plantaInput']) && isset($_POST['espaco_id'])) {
        $space_id = $_POST['espaco_id'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["plantaInput"]["name"]);

        if (move_uploaded_file($_FILES["plantaInput"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO plantas (planta_path) VALUES (?)");
            $stmt->bind_param("s", $target_file);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'planta_path' => $target_file]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erro ao salvar no banco de dados']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao fazer upload do arquivo']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    }
}

$conn->close();
?>

