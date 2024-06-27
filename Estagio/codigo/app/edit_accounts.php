<?php
session_start();
include 'config.php';

// Verificar se o usuário está logado e é um superadmin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit;
}

// Processar as atualizações
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $is_superadmin = isset($_POST['is_superadmin']) ? 1 : 0;
    $new_password = $_POST['new_password'];

    // Atualizar com ou sem nova senha
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE organizadores SET name = ?, email = ?, is_superadmin = ?, password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssisi", $name, $email, $is_superadmin, $hashed_password, $id);
    } else {
        $update_sql = "UPDATE organizadores SET name = ?, email = ?, is_superadmin = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssii", $name, $email, $is_superadmin, $id);
    }

    if ($update_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Organizador atualizado com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar organizador: ' . $update_stmt->error]);
    }
    $update_stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requisição inválida.']);
}

$conn->close();
?>
