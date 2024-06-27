<?php
include 'config.php';

// Se o método de requisição for POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta SQL para verificar se o usuário existe
    $sql = "SELECT id, email, name, password, is_superadmin FROM organizadores WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se o usuário for encontrado
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verifica se a senha é válida
        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];

            // Verifica se o usuário é um superadmin
            if ($user['is_superadmin'] == 1) {
                $_SESSION['is_superadmin'] = true; // Definindo is_superadmin como true
            } else {
                $_SESSION['is_superadmin'] = false; // Definindo is_superadmin como false
            }

            // Redirecionamento com base no status de superadmin
            if ($_SESSION['is_superadmin']) {
                header("Location: superadmin.php");
            } else {
                header("Location: organizador.php");
            }
            exit; // Sempre saia após redirecionar
        } else {
            $error = "Senha incorreta.";
        }
    } else {
        $error = "Email não encontrado.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="http://digitalprogression.pt/img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
                DigitalProgression
            </a>
            <a class="btn btn-outline-light" href="http://digitalprogression.pt/" target="_blank">Saber Mais</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Login</h4>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($error)) { ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo $error; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col"><a href="https://digitalprogression.pt/" target="_blank"><img src="img/img01.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://www.cm-tabua.pt/" target="_blank"><img src="img/img02.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://www.cm-tabua.pt/viver/feiras-e-mercados/facit/" target="_blank"><img src="img/img03.png" class="img-fluid"></a></div>
            <div class="col"><a href="https://espaco-cultiva.pt/home/" target="_blank"><img src="img/img04.png" class="img-fluid"></a></div>
        </div>

        <footer class="text-muted m-4">
            <hr>
            2023 &copy; Todos os direitos reservados a <a href="https://digitalprogression.pt/" target="_blank">DigitalProgression, Lda.</a>
        </footer>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>