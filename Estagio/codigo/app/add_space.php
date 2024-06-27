<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $spaceName = $_POST['space_name'];
    $spaceDescription = $_POST['space_description'];

    // Prepara a consulta SQL para inserir o espaço na base de dados
    $sql = "INSERT INTO espacos (nome, descricao) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $spaceName, $spaceDescription);

    // Executa a consulta
    if ($stmt->execute()) {
        echo "Espaço adicionado com sucesso.";
    } else {
        echo "Erro ao adicionar Espaço" . $stmt->error;
    }

    $stmt->close();
}
?>




