<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_superadmin = isset($_POST['is_superadmin']) ? 1 : 0; 

    // Verifica se o email já existe na base de dados
    $check_sql = "SELECT id FROM organizadores WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Se o email já existe, exibe uma mensagem de erro
        echo "O email já está em uso. Por favor, escolha outro.";
        echo "<br>";
        echo "<a href='superadmin.php'>Voltar</a>";
    } else {
        $insert_sql = "INSERT INTO organizadores (name, email, password, is_superadmin) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssi", $name, $email, $password, $is_superadmin);
        $insert_stmt->execute();

        if ($insert_stmt->affected_rows > 0) {
            echo "Organizador registrado com sucesso!";
            echo "<br>";
            echo "<a href='superadmin.php'>Ir para Contas</a>";
            echo "<br>";
            echo "<a href='superadmin.php'>Voltar</a>";
        } else {
            echo "Erro ao registrar organizador: " . $conn->error;
            echo "<br>";
            echo "<a href='superadmin.php'>Voltar</a>";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    header("Location: superadmin.html");
    exit();
}

?>